<?php

namespace Drupal\unomi\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\unomi\UnomiCookieManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a 'Unomi Segment Selection' condition.
 *
 * @Condition(
 *   id = "unomi_segment_selection",
 *   label = @Translation("Unomi Segment Selection"),
 *   description = @Translation("Allows to select a segment from Unomi and
 *   correlate it with a cookie.")
 * )
 */
class SegmentSelection extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * @var \Drupal\unomi\UnomiCookieManager
   */
  protected $cookieManager;

  /**
   * @var \Drupal\Component\Plugin\Context\ContextInterface[]|object|null
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $class = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $class->cookieManager = $container->get('unomi.cookie_manager');
    $class->currentUser = $container->get('current_user');
    return $class;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Grab our service.
    /** @var \Drupal\unomi\UnomiApiInterface $unomiService */
    $unomiService = \Drupal::service('unomi_api');

    // Get the option list of segments from Apache Unomi.
    $allowedValues = $unomiService->getSegments();

    $form['unomi_segment_selection'] = [
      '#title' => $this->t('Unomi Segment Selection'),
      '#type' => 'select',
      '#options' => $allowedValues,
      '#description' => $this->t('For which segment this content
      should be visible. Leaving empty (default) will show the content for
      everyone. "Unknown segment" will only show the content until a
      segment has been determined for the visitor.'),
      '#default_value' => $this->getSelectedSegments(),
      '#multiple' => TRUE,
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('unomi_segment_selection', []);
    // Has to match static::defaultConfiguration exactly to be registered as empty.
    $this->configuration['unomi_segment_selection'] = empty($value) ? [] : $value;
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    if ($this->configuration['unomi_segment_selection']) {
      $contexts[] = 'cookies:' . $this->cookieManager->getCookieName();
    }
    return $contexts;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    $segments = $this->configuration['unomi_segment_selection'] ?? [];
    if (!$segments) {
      return $this->t('No Unomi Segments selected');
    }
    $segment_string = implode(', ', $segments);
    return $this->t('The Unomi Segment(s) selected is @segment', ['@segment' => $segment_string]);
  }

  /**
   * @return array
   */
  protected function getSelectedSegments() {
    return $this->configuration['unomi_segment_selection'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $segments = $this->getSelectedSegments();

    if (empty($segments) && !$this->isNegated()) {
      return TRUE;
    }

    // If the user has administer content, give them access.
    if ($this->currentUser->hasPermission('administer content')) {
      return TRUE;
    }

    $cookieValues = $this->cookieManager->getSegmentCookieValues();
    foreach ($segments as $segment) {
      // If cookie value matches one of the segments, allow access.
      foreach ($cookieValues as $cookieValue) {
        if ($cookieValue === (string) $segment) {
          return TRUE;
        }
      }
    }

    return FALSE;

  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['unomi_segment_selection' => []] + parent::defaultConfiguration();
  }

}

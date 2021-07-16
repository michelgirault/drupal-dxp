<?php

namespace Drupal\rocketship_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\rocketship_core\Plugin\Field\FieldType\ParagraphTitleReplacement;
use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;

/**
 * Plugin implementation of the 'contentblock_title_replacement_formatter'
 * formatter.
 *
 * @FieldFormatter(
 *   id = "contentblock_title_replacement_formatter",
 *   label = @Translation("Title Replacement Formatter"),
 *   field_types = {
 *     "contentblock_title_replacement"
 *   }
 * )
 */
class ContentBlockTitleReplacementFormatter extends TextDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'wrapper_override' => 'nothing',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['wrapper_override'] = [
      '#type' => 'select',
      '#title' => $this->t('Override wrapper selection'),
      '#description' => $this->t('Select a tag to wrap this output in, overriding the selection made by the client.'),
      '#default_value' => $this->getSetting('wrapper_override'),
      '#options' => [
        'nothing' => $this->t('Nothing'),
        'h1' => $this->t('h1'),
        'h2' => $this->t('h2'),
        'h3' => $this->t('h3'),
        'h4' => $this->t('h4'),
        'h5' => $this->t('h5'),
        'h6' => $this->t('h6'),
        'span' => $this->t('span'),
      ],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary[] = t('Wrapper override: @override', ['@override' => $this->getSetting('wrapper_override')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      // Grab tag that the client chose.
      $tag = $item->wrapper ?: 'h1';
      // Make sure it's a legal choice.
      if (!in_array($tag, ParagraphTitleReplacement::getPossibleOptions())) {
        $tag = 'h1';
      }
      if ($this->getSetting('wrapper_override') !== 'nothing') {
        $tag = $this->getSetting('wrapper_override');
      }

      if ($item->replace) {
        $value = $item->value;
      }
      else {
        $value = $item->value;
        // Show the current node title.
        /** @var \Drupal\node\NodeInterface $node */
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node) {
          if (!is_object($node)) {
            $node = Node::load($node);
          }
          if ($node->hasTranslation($langcode)) {
            $node = $node->getTranslation($langcode);
          }
          $value = $node->getTitle();
        }
        else {
          preg_match('/node\.([0-9]+)/', \Drupal::routeMatch()->getRawParameter('section_storage'), $matches);
          if (!empty($matches[1])) {
            $node = $matches[1];
            if ($node) {
              if (!is_object($node)) {
                $node = Node::load($node);
              }
              if ($node->hasTranslation($langcode)) {
                $node = $node->getTranslation($langcode);
              }
              $value = $node->getTitle();
            }
          }
        }
      }

      $elements[$delta] = [
        '#prefix' => '<' . $tag . '>',
        '#suffix' => '</' . $tag . '>',
        '#markup' => $value,
        '#allowed_tags' => [
          'em',
          'strong',
        ],
      ];
    }

    return $elements;
  }

}

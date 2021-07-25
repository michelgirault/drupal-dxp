<?php

namespace Drupal\unomi\UnomiConnector;

use Dropsolid\UnomiSdkPhp\Unomi;
use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Url;
use Drupal\unomi\UnomiConnectorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base class for Unomi connector plugins.
 *
 * Plugins extending this class need to define a plugin definition array through
 * annotation. These definition arrays may be altered through
 * hook_unomi_connector_info_alter(). The definition includes the
 * following keys:
 * - id: The unique, system-wide identifier of the backend class.
 * - label: The human-readable name of the backend class, translated.
 * - description: A human-readable description for the backend class,
 *   translated.
 *
 * A complete plugin definition should be written as in this example:
 *
 * @code
 * @UnomiConnector(
 *   id = "my_connector",
 *   label = @Translation("My connector"),
 *   description = @Translation("Authenticates with SuperAuth™.")
 * )
 * @endcode
 *
 * @see \Drupal\unomi\Annotation\UnomiConnector
 * @see \Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager
 * @see \Drupal\unomi\UnomiConnectorInterface
 * @see plugin_api
 */
abstract class UnomiConnectorPluginBase extends PluginBase implements UnomiConnectorInterface, PluginFormInterface {

  /**
   * A connection to the Unomi server.
   *
   * @var \Dropsolid\UnomiSdkPhp\Http\ApiClient\
   */
  protected $client;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'scheme' => 'https',
      'host' => 'localhost',
      'port' => 8181,
      'path' => '/',
      'timeout' => 5,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $plugin_definition = $this->getPluginDefinition();
    return $plugin_definition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $plugin_definition = $this->getPluginDefinition();
    return isset($plugin_definition['description']) ? $plugin_definition['description'] : '';
  }

  /**
   * Returns the Unomi server URI.
   *
   * @return string
   *   The validated URL to Unomi
   */
  protected function getServerUri() {

    $scheme = isset($this->configuration['scheme']) ? $this->configuration['scheme'] . '://' : '';
    $host = isset($this->configuration['host']) ? $this->configuration['host'] : '';
    $port = isset($this->configuration['port']) && $this->configuration['port'] !== 0 ? ':' . $this->configuration['port'] : 8181;
    $path = isset($this->configuration['path']) ? $this->configuration['path'] : '';
    $uri = "$scheme$host$port$path";
    $url = Url::fromUri($uri);

    // Return the validated URL.
    return $url->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $configuration['port'] = (int) $configuration['port'];
    $configuration['timeout'] = (int) $configuration['timeout'];
    $this->configuration = $configuration + $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['scheme'] = [
      '#type' => 'select',
      '#title' => $this->t('HTTP protocol'),
      '#description' => $this->t('The HTTP protocol to use for sending queries.'),
      '#default_value' => isset($this->configuration['scheme']) ? $this->configuration['scheme'] : 'http',
      '#options' => [
        'http' => 'http',
        'https' => 'https',
      ],
    ];

    $form['host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unomi host'),
      '#description' => $this->t('The host name or IP of your Unomi server, e.g. <code>localhost</code> or <code>www.example.com</code>.'),
      '#default_value' => isset($this->configuration['host']) ? $this->configuration['host'] : '',
      '#required' => TRUE,
    ];

    $form['port'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unomi port'),
      '#description' => $this->t('The Jetty example server is at port 8181, while Tomcat uses 8080 by default.'),
      '#default_value' => isset($this->configuration['port']) ? $this->configuration['port'] : '',
      '#required' => TRUE,
    ];

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unomi path'),
      '#description' => $this->t('The path that identifies the Unomi instance to use on the server.'),
      '#default_value' => isset($this->configuration['path']) ? $this->configuration['path'] : '/',
    ];

    $form['timeout'] = [
      '#type' => 'number',
      '#min' => 1,
      '#max' => 180,
      '#title' => $this->t('Query timeout'),
      '#description' => $this->t('The timeout in seconds for search queries sent to the Unomi server.'),
      '#default_value' => isset($this->configuration['timeout']) ? $this->configuration['timeout'] : 5,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if (isset($values['port']) && (!is_numeric($values['port']) || $values['port'] < 0 || $values['port'] > 65535)) {
      $form_state->setError($form['port'], $this->t('The port has to be an integer between 0 and 65535.'));
    }
    if (!empty($values['path']) && strpos($values['path'], '/') !== 0) {
      $form_state->setError($form['path'], $this->t('If provided the path has to start with "/".'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    if ($this instanceof ConfigurableInterface) {
      $this->setConfiguration($form_state->getValues());
    }
    // Clear the entity field info and entity types as that is where we attach
    // our Segments to.
    Cache::invalidateTags(['entity_types', 'entity_field_info', 'unomi:segments']);
  }

  /**
   * {@inheritdoc}
   */
  public function getClient() {
    $api_client = $this->getApiClient();
    if (!$api_client) {
      return FALSE;
    }
    return Unomi::withDefaultSerializer($api_client);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function onDependencyRemoval(array $dependencies) {
    // By default, we're not reacting to anything and so we should leave
    // everything as it was.
    return FALSE;
  }

}

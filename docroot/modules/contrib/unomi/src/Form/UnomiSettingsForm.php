<?php

namespace Drupal\unomi\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\unomi\UnomiApiInterface;
use Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Unomi Integration settings for this site.
 */
class UnomiSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'unomi.settings';

  /**
   * The Unomi API service.
   *
   * @var \Drupal\unomi\UnomiApiInterface
   */
  protected $unomiApi;

  /**
   * The backend plugin manager.
   *
   * @var \Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager
   */
  protected $unomiConnectorPluginManager;

  /**
   * The messenger to send info or warnings to Drupal with.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The Unomi configuration object.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $configuration;

  /**
   * UnomiSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\unomi\UnomiApiInterface $unomi_api
   *   The Unomi API service.
   * @param \Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager $unomi_connector_plugin_manager
   *   The Unomi Connector Plugin Manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger to send info or warnings to Drupal with.
   */
  public function __construct(ConfigFactoryInterface $config_factory, UnomiApiInterface $unomi_api, UnomiConnectorPluginManager $unomi_connector_plugin_manager, MessengerInterface $messenger) {
    parent::__construct($config_factory);
    $this->unomiApi = $unomi_api;
    $this->unomiConnectorPluginManager = $unomi_connector_plugin_manager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('unomi_api'),
      $container->get('plugin.manager.unomi.connector'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'unomi_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $configuration = $this->configFactory->getEditable(static::SETTINGS);
    $unomi_connector_options = $this->getUnomiConnectorOptions();
    $form['connector'] = [
      '#type' => 'radios',
      '#title' => $this->t('Unomi Connector'),
      '#description' => $this->t('Choose a connector to use for this Unomi server.'),
      '#empty_value' => '',
      '#options' => $unomi_connector_options,
      '#default_value' => $configuration->get('connector'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [get_class($this), 'buildAjaxUnomiConnectorConfigForm'],
        'wrapper' => 'unomi-connector-config-form',
        'method' => 'replace',
        'effect' => 'fade',
      ],
    ];

    $cookie_name = $configuration->get('cookie_name');
    $form['cookie_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookie Name'),
      '#default_value' => isset($cookie_name) ? $cookie_name : 'DS_VARY_ML',
      '#required' => TRUE,
      '#description' => $this->t('Name of the cookie to be used to get segments'),
    ];

    $this->buildConnectorConfigForm($form, $form_state);

    $form['actions']['submit']['#value'] = $this->t('Connect to Unomi');

    return parent::buildForm($form, $form_state);
  }

  /**
   * Handles switching the selected Unomi connector plugin.
   */
  public static function buildAjaxUnomiConnectorConfigForm(array $form, FormStateInterface $form_state) {
    // The work is already done in form(), where we rebuild the entity according
    // to the current form values and then create the backend configuration form
    // based on that. So we just need to return the relevant part of the form
    // here.
    return $form['connector_config'];
  }

  /**
   * Builds the backend-specific configuration form.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function buildConnectorConfigForm(array &$form, FormStateInterface $form_state) {
    $form['connector_config'] = [];
    $configuration = $this->configFactory->getEditable(static::SETTINGS);

    $connector_id = $configuration->get('connector');
    if ($form_state->getValue('connector') != '') {
      // It is due to the ajax.
      $connector_id = $form_state->getValue('connector');
    }

    $connector_config = [];
    if (!empty($configuration->get('connector_config'))) {
      $connector_config = $configuration->get('connector_config');
    }

    if ($connector_id) {
      $connector = $this->unomiConnectorPluginManager->createInstance($connector_id, $connector_config);
      if ($connector instanceof PluginFormInterface) {
        $form_state->set('connector', $connector_id);
        if ($form_state->isRebuilding()) {
          $this->messenger->addWarning($this->t('Please configure the selected Unomi connector.'));
        }
        // Attach the Unomi connector plugin configuration form.
        $connector_form_state = SubformState::createForSubform($form['connector_config'], $form, $form_state);
        $form['connector_config'] = $connector->buildConfigurationForm($form['connector_config'], $connector_form_state);

        // Modify the backend plugin configuration container element.
        $form['connector_config']['#type'] = 'details';
        $form['connector_config']['#title'] = $this->t('Configure %plugin Unomi connector', ['%plugin' => $connector->label()]);
        $form['connector_config']['#description'] = $connector->getDescription();
        $form['connector_config']['#open'] = TRUE;
      }
    }
    $form['connector_config'] += ['#type' => 'container'];
    $form['connector_config']['#attributes'] = [
      'id' => 'unomi-connector-config-form',
    ];
    $form['connector_config']['#tree'] = TRUE;

    try {
      $cid = $form_state->getValue('connector') ? $form_state->getValue('connector') : $connector_id;
      $cconfig = $form_state->getValue('connector_config') ? $form_state->getValue('connector_config') : $connector_config;
      if (isset($cid) && isset($cconfig)) {
        if ($connector instanceof PluginFormInterface) {
          $connector = $this->unomiConnectorPluginManager->createInstance($cid, $cconfig);

          $form['status'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Connection Status'),
            '#open' => TRUE,
            '#description' => $connector->getClient()->segments()->listSegments() ? $this->t('Connection successfully established via @type!', ['@type' => $connector_id]) : $this->t('Connection failed, there was a problem with the connection to the UNOMI API instance.'),
            '#weight' => -1,
          ];
        }
      }
    }
    catch (\Exception $e) {
      watchdog_exception('error', $e);
      $form['status'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Connection Status'),
        '#open' => TRUE,
        '#description' => $this->t('Connection failed, there was a problem with the connection to the UNOMI API instance.'),
        '#weight' => -1,
      ];
    }

  }

  /**
   * Returns all available backend plugins, as an options list.
   *
   * @return string[]
   *   An associative array mapping backend plugin IDs to their (HTML-escaped)
   *   labels.
   */
  protected function getUnomiConnectorOptions() {
    $options = [];
    foreach ($this->unomiConnectorPluginManager->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = Html::escape($plugin_definition['label']);
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Load the configuration to get our existing state. This way we can save
    // our password.
    $configuration = $this->config(static::SETTINGS);
    $connector = $this->unomiConnectorPluginManager->createInstance($form_state->getValue('connector'), $configuration->get('connector_config'));
    if ($connector instanceof PluginFormInterface) {
      $connector_form_state = SubformState::createForSubform($form['connector_config'], $form, $form_state);
      $connector->validateConfigurationForm($form['connector_config'], $connector_form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Load the configuration to get our existing state. This way we can save
    // our password.
    $configuration = $this->config(static::SETTINGS);
    $connector = $this->unomiConnectorPluginManager->createInstance($form_state->getValue('connector'), $configuration->get('connector_config'));
    if ($connector instanceof PluginFormInterface) {
      $connector_form_state = SubformState::createForSubform($form['connector_config'], $form, $form_state);
      $connector->submitConfigurationForm($form['connector_config'], $connector_form_state);
      // Overwrite the form values with type casted values.
      // @see \Drupal\unomi\UnomiConnector\UnomiConnectorPluginBase::setConfiguration()
      $form_state->setValue('connector_config', $connector->getConfiguration());
    }

    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set cookie name
      ->set('cookie_name', $form_state->getValue('cookie_name'))
      // Set the submitted configuration setting.
      ->set('connector', $form_state->getValue('connector'))
      // You can set multiple configurations at once by making
      // multiple calls to set().
      ->set('connector_config', $form_state->getValue('connector_config'))
      ->save();

    // @todo Test the connection with Unomi here. Possible another button to
    // verify?
    parent::submitForm($form, $form_state);
  }

}

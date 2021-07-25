<?php

namespace Drupal\unomi;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Unomi Integration service.
 */
class UnomiApi implements UnomiApiInterface {

  use StringTranslationTrait;

  /**
   * The Dropsolid Personalization config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $unomiConfig;

  /**
   * Drupal state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The current request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The messenger to send info or warnings to Drupal with.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Unomi Connector Plugin Manager.
   *
   * @var \Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager
   */
  protected $unomiConnectorPluginManager;

  /**
   * Logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Cache Backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Constructs a UnomiApi class.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL Generator service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger to send info or warnings to Drupal with.
   * @param \Drupal\unomi\UnomiConnector\UnomiConnectorPluginManager $unomi_connector_plugin_manager
   *   The Unomi Connector Plugin Manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The Unomi logger.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Cache bin specificly for Unomi.
   */
  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state, UrlGeneratorInterface $url_generator, RequestStack $request_stack, MessengerInterface $messenger, UnomiConnectorPluginManager $unomi_connector_plugin_manager, LoggerInterface $logger, CacheBackendInterface $cache) {
    $this->unomiConfig = $config_factory->get('unomi.settings');
    $this->state = $state;
    $this->urlGenerator = $url_generator;
    $this->request = $request_stack->getCurrentRequest();
    $this->messenger = $messenger;
    $this->unomiConnectorPluginManager = $unomi_connector_plugin_manager;
    $this->logger = $logger;
    $this->cacheBackend = $cache;
  }

  /**
   * {@inheritdoc}
   */
  public function getClient() {
    $connector = $this->unomiConfig->get('connector');
    $connector_config = $this->unomiConfig->get('connector_config');
    if ($connector && $connector_config && $this->unomiConnectorPluginManager->hasDefinition($connector)) {
      // Ask the plugin to give us the Unomiclient.
      /** @var \Drupal\unomi\UnomiConnectorInterface $connector */
      $connector = $this->unomiConnectorPluginManager->createInstance($connector, $connector_config);
      return $connector->getClient();
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getApiClient() {
    $connector = $this->unomiConfig->get('connector');
    $connector_config = $this->unomiConfig->get('connector_config');
    if ($connector && $connector_config && $this->unomiConnectorPluginManager->hasDefinition($connector)) {

      // Ask the plugin to give us the Unomiclient.
      /** @var \Drupal\unomi\UnomiConnectorInterface $connector */
      $connector = $this->unomiConnectorPluginManager->createInstance($connector, $connector_config);
      return $connector->getApiClient();
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getSegments() {
    $cid = 'unomi:segments';
    $dataCached = $this->cacheBackend->get($cid);
    if ($dataCached) {
      return $dataCached->data;
    }

    $segmentList = [
      '_unknown' => $this->t('Unknown segment'),
    ];
    if ($this->getClient()) {
      try {
        // Grab the segments from Unomi.
        $segments = $this->getClient()->segments()->listSegments();
        // Generate list of allowed values from the Unomi segment API.
        foreach ($segments as $segment) {
          $segmentList[$segment->getId()] = $segment->getName();
        }
        // Store the tree into the cache when successful.
        $this->cacheBackend->set($cid, $segmentList, CacheBackendInterface::CACHE_PERMANENT, ['unomi:segments']);
      }
      catch (\Throwable $e) {
        if ($e->getPrevious() instanceof IdentityProviderException) {
          $body = NULL;
          /** @var \League\OAuth2\Client\Provider\Exception\IdentityProviderException $providerException */
          $providerException = $e->getPrevious();
          $body = $providerException->getResponseBody();
          $this->logger->error(
            'Error getting the Apache Unomi segments: @message with response code @code and body @body',
            [
              '@message' => $e->getMessage(),
              '@code' => $e->getPrevious()->getCode(),
              '@body' => $body,
            ]
          );
        }
        else {
          $this->logger->error(
            'Error getting the Apache Unomi segments: @message',
            [
              '@message' => $e->getMessage(),
            ]
          );
        }
      }
    }

    return $segmentList;
  }

}

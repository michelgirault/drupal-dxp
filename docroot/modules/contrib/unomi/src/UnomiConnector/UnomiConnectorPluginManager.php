<?php

namespace Drupal\unomi\UnomiConnector;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * A plugin manager for Unomi connector plugins.
 *
 * @see \Drupal\unomi\Annotation\UnomiConnector
 * @see \Drupal\unomi\UnomiConnectorInterface
 * @see \Drupal\unomi\UnomiConnector\UnomiConnectorPluginBase
 *
 * @ingroup plugin_api
 */
class UnomiConnectorPluginManager extends DefaultPluginManager {

  /**
   * Constructs a UnomiConnectorManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $this->alterInfo('unomi_connector_info');
    $this->setCacheBackend($cache_backend, 'unomi_connector_plugins');

    parent::__construct('Plugin/UnomiConnector', $namespaces, $module_handler, 'Drupal\unomi\UnomiConnectorInterface', 'Drupal\unomi\Annotation\UnomiConnector');
  }

}

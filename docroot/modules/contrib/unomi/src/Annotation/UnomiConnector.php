<?php

namespace Drupal\unomi\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a connector plugin annotation object.
 *
 * Condition plugins provide generalized conditions for use in other
 * operations, such as conditional block placement.
 *
 * Plugin Namespace: Plugin\UnomiConnector
 *
 * @see \Drupal\unomi\UnomiConnector\UnomiConnectorManager
 * @see \Drupal\unomi\UnomiConnectorInterface
 * @see \Drupal\unomi\UnomiConnector\UnomiConnectorPluginBase
 *
 * @ingroup plugin_api
 *
 * @Annotation
 */
class UnomiConnector extends Plugin {

  /**
   * The Unomi connector plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the Unomi connector.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The backend description.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}

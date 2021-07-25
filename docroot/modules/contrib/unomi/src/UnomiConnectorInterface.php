<?php

namespace Drupal\unomi;

use Drupal\Component\Plugin\ConfigurableInterface;

/**
 * The Unomi connector interface.
 */
interface UnomiConnectorInterface extends ConfigurableInterface {

  /**
   * Return a Guzzle Api Client.
   *
   * @return \Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClient
   *   The Unomi Guzzle Apiclient.
   */
  public function getApiClient();

  /**
   * Return a Unomi Client.
   *
   * @return \Dropsolid\UnomiSdkPhp\Unomi
   *   The Unomi Client Helper with access to the SDK functions.
   */
  public function getClient();

}

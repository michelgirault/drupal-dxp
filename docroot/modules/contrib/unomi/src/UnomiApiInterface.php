<?php

namespace Drupal\unomi;

/**
 * Interface for UnomiApi service.
 */
interface UnomiApiInterface {

  /**
   * Get client object, to interact with Unomi data repositories.
   *
   * @return \Dropsolid\UnomiSdkPhp\Unomi|bool
   *   Unomi client object.
   */
  public function getClient();

  /**
   * Get a Unomi API client.
   *
   * @return \Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClient|null
   *   The Unomi API client object.
   */
  public function getApiClient();

  /**
   * Get the Unomi Segments as an option list.
   *
   * Return array
   *   An array with the UUID & Name.
   */
  public function getSegments();

}

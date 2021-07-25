<?php

namespace Drupal\unomi\Plugin\UnomiConnector;

use Dropsolid\UnomiSdkPhp\Http\Guzzle\GuzzleApiClientFactory;
use Drupal\unomi\UnomiConnector\BasicAuthTrait;
use Drupal\unomi\UnomiConnector\UnomiConnectorPluginBase;

/**
 * Basic auth Unomi connector.
 *
 * @UnomiConnector(
 *   id = "basic_auth",
 *   label = @Translation("Basic Auth"),
 *   description = @Translation("A connector usable for Unomi installations protected by basic authentication.")
 * )
 */
class BasicAuthUnomiConnector extends UnomiConnectorPluginBase {

  use BasicAuthTrait;

  /**
   * {@inheritdoc}
   */
  public function getApiClient() {
    return GuzzleApiClientFactory::createBasicAuth(
      [
        'timeout' => 3.0,
        'base_uri' => $this->getServerUri(),
        'auth' => [$this->configuration['username'], $this->configuration['password']],
      ]
    );
  }

}

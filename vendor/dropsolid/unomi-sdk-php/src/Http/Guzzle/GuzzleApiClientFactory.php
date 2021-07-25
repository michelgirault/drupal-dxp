<?php

namespace Dropsolid\UnomiSdkPhp\Http\Guzzle;

use Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClient;
use GuzzleHttp\Client as GuzzleHttpClient;
use Http\Adapter\Guzzle6\Client;

/**
 * Class GuzzleApiClientFactory
 *
 * @package Uno\UnomiSdkPhp\Http\Guzzle
 */
class GuzzleApiClientFactory
{
    /**
     * @param array $config
     * @return ApiClient
     */
    public static function createBasicAuth(
        array $config = []
    ) {
    
        if (!isset($config['handler'])) {
            if (!isset($config['callback'])) {
                $config['callback'] = null;
            }
        }
        if (!isset($config['options'])) {
            $config['options'] = [];
        }

        $httpClient = new GuzzleHttpClient($config);
        $psrClient = new Client($httpClient);

        return new ApiClient(null, $psrClient, null, $config);
    }
}

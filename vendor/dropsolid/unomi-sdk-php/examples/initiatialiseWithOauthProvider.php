<?php

use Dropsolid\OAuth2\Client\Http\Guzzle\Middleware\RefreshTokenMiddleware;
use Dropsolid\OAuth2\Client\Provider\DropsolidPlatform;
use Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClient;
use Dropsolid\UnomiSdkPhp\Unomi;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\HandlerStack;
use Http\Adapter\Guzzle6\Client;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * @param AbstractProvider $provider
 * @param AccessTokenInterface|array $accessToken
 * @param array $config
 * @return ApiClient
 */
function createOauthApiClient(
    AbstractProvider $provider,
    AccessTokenInterface $accessToken,
    array $config = []
) {

    if (!isset($config['handler'])) {
        if (!isset($config['callback'])) {
            $config['callback'] = null;
        }
        $handler = initialiseHandlerStack(
            $provider,
            $accessToken,
            $config['callback']
        );

        $config['handler'] = $handler;
    }
    if (!isset($config['options'])) {
        $config['options'] = [];
    }

    $httpClient = new GuzzleHttpClient($config);
    $psrClient = new Client($httpClient);

    return new ApiClient($provider, $psrClient, $accessToken, $config);
}

/**
 * @param AbstractProvider $provider
 * @param AccessTokenInterface $accessToken
 * @param callable $refreshTokenCallback
 * @return HandlerStack
 */
function initialiseHandlerStack(
    AbstractProvider $provider,
    AccessTokenInterface $accessToken,
    callable $refreshTokenCallback = null
) {

    $handlerStack = HandlerStack::create();
    $refreshMiddleware = new RefreshTokenMiddleware(
        $provider,
        $accessToken,
        $refreshTokenCallback
    );
    $handlerStack->push($refreshMiddleware);

    return $handlerStack;
}

$provider = new DropsolidPlatform(
    [
        'clientId' => 'your-client-id',
        'clientSecret' => 'your-client-secret',
        'urlAuthorize' => 'https://admin.platform.dropsolid.com/oauth/authorize',
        'urlAccessToken' => 'https://admin.platform.dropsolid.com/oauth/token',
        'urlResourceOwnerDetails' => 'https://admin.platform.dropsolid.com/oauth/user.info',
        'scopes' => 'cdp_admin',
    ]
);

try {
    $accessToken = $provider->getAccessToken('client_credentials');
    $apiClient = createOauthApiClient(
        $provider,
        $accessToken,
        ['timeout' => 3.0, 'base_uri' => 'https://unomi.poc.qa.dropsolid-sites.com']
    );

    $unomi = Unomi::withDefaultSerializer($apiClient);

// List all segments using the Oauth provider and middleware.
    $segments = $unomi->segments()->listSegments();
} catch (\Http\Client\Exception $e) {
    // It's an example, we don't do anything with it.
} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    // It's an example, we don't do anything with it.
}

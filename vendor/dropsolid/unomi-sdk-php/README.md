# Unomi SDK PHP
PHP client to connect to the [Unomi API][unomi-docs].

## Installation
The package is available via composer:

```Bash
$ composer require dropsolid/unomi-sdk-php
```

## Basic usage

### Dependencies

This package requires an HTTP client supported by [PHP-HTTP][php-http-homepage].
A default implementation has been provided if you use [Guzzle][guzzle-homepage]
(`guzzlehttp/guzzle`).

### Setting up the opinionated Guzzle client using Basic Auth Authentication

```php
use Dropsolid\UnomiSdkPhp\Http\Guzzle\GuzzleApiClientFactory;

$apiClient = GuzzleApiClientFactory::createBasicAuth(
    [
        'timeout' => 3.0,
        'base_uri' => 'localhost',
        'auth' => ['karaf', 'karaf']
    ]
);
```

### Making requests using the repository classes

If you want to work with deserialized models, you can make use of the
`Unomi` class. This acts as a wrapper for the API. You'll need
to install [Symfony's Serializer component][symfony-serializer]
(`symfony/serializer`) for this.

Instantiate Unomi as shown below to make use of the repositories
 
```php
<?php

use Dropsolid\UnomiSdkPhp\Unomi;

$unomi = Unomi::withDefaultSerializer($apiClient);

// Performing a segment.list request with offset
$offset = [
    'offset' => 0,
];

// Get all itemIds & conditions from all existing segments
$segments = $unomi->segments()->listSegments($offset);
foreach ($segments as $segmentMetadata) {
    $id = $segmentMetadata->getId();
    // Performing a segment.info request
    $segment = $unomi->segments()->getSegment($id);
    var_dump($segment->getItemId());
    var_dump($segment->getCondition());
}

```

### Authentication

The Unomi API could have a middleware with OAuth2.0 in front or use simple basic auth. The API allows to use both and
for the OAuth2 implementation we rely on [The PHP league's OAuth2 client][league-oauth-homepage]. This was optimized
 for use with the third-party [Dropsolid Platform OAuth 2.0 provider][dropsolid-platform-oauth-2] to handle this.

#### Basic Auth Example
```php
<?php

use Dropsolid\UnomiSdkPhp\Http\Guzzle\GuzzleApiClientFactory;
use Dropsolid\UnomiSdkPhp\Unomi;

$apiClient = GuzzleApiClientFactory::createBasicAuth(
    [
        'timeout' => 3.0,
        'base_uri' => 'localhost',
        'auth' => ['karaf', 'karaf']
    ]
);

$unomi = Unomi::withDefaultSerializer($apiClient);

```

#### OAuth Example using Dropsolid Platform

```php
<?php

use Dropsolid\UnomiSdkPhp\Http\Guzzle\GuzzleApiClientFactory;
use Dropsolid\OAuth2\Client\Provider\DropsolidPlatform;

$provider = new DropsolidPlatform(
    [
        'clientId' => 'your-client-id',
        'clientSecret' => 'your-client-secret',
        'urlAuthorize'            => 'https://admin.platform.dropsolid.com/oauth/authorize',
        'urlAccessToken'          => 'https://admin.platform.dropsolid.com/oauth/token',
        'urlResourceOwnerDetails' => 'https://admin.platform.dropsolid.com/oauth/user.info',
        'scopes' => 'cdp_admin',
    ]
);
$accessToken = $provider->getAccessToken('client_credentials');

$apiClient = GuzzleApiClientFactory::createOauth(
    $provider,
    $accessToken,
    ['timeout' => 3.0, 'base_uri' => 'https://unomi.poc.qa.dropsolid-sites.com']
);

```

#### Creating custom API Clients with PSR compatible HttpClients

```php

<?php

use Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClient;
use GuzzleHttp\Client as GuzzleHttpClient;
use Http\Adapter\Guzzle6\Client;

$config = [ // Optional extra configuration.
  'timeout' => 3.0, 
  'base_uri' => 'https://unomi.poc.qa.dropsolid-sites.com'
];

// Create a PHP-HTTP compatible client.
$httpClient = new GuzzleHttpClient($config);
$psrClient = new Client($httpClient);

// Alternatively, you can use any other PHP-HTTP compatible client of your
// choosing to instantiate the API client.
$apiClient = new ApiClient(
    $provider,
    $psrClient,
    null
);
```

###  Known issues
Only the listing of segments and the segment details are implemented so far.

[unomi-docs]: https://unomi.apache.org/rest-api-doc/index.html
[php-http-homepage]: http://docs.php-http.org/en/latest/
[league-oauth-homepage]: http://oauth2-client.thephpleague.com/
[dropsolid-platform-oauth-2]: https://gitlab.com/dropsolid/oauth2-dropsolid-platform
[league-usage]: http://oauth2-client.thephpleague.com/usage/
[guzzle-homepage]: https://github.com/guzzle/guzzle
[symfony-serializer]: https://symfony.com/doc/current/components/serializer.html

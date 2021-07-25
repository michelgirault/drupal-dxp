<?php

namespace Dropsolid\UnomiSdkPhp\Http\ApiClient;

use Dropsolid\UnomiSdkPhp\Request\Attributes\Offset\OffsetInterface;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Size\SizeInterface;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Sort\SortInterface;
use Dropsolid\UnomiSdkPhp\Request\RequestInterface;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Class ApiClient
 *
 * @package Dropsolid\UnomiSdkPhp\Http
 */
class ApiClient implements ApiClientInterface
{
    /**
     * @var AbstractProvider
     */
    protected $provider;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var string
     */
    private $defaultMethod;

    /**
     * @var string Unomi Base URL to use
     */
    private $baseUri = '';

    /**
     * ApiClient constructor.
     *
     * @param AbstractProvider|null $provider
     * @param HttpClient $httpClient
     * @param AccessTokenInterface|null $accessToken
     * @param array $options
     *   An associative array of options. Supports the following values:
     *   - `default_method`: the default method to use when a requests supports
     *       multiple methods.
     */
    public function __construct(
        $provider,
        HttpClient $httpClient,
        $accessToken,
        array $options = []
    ) {

        $this->httpClient = $httpClient;
        $this->provider = $provider;
        $this->accessToken = $accessToken;
        $this->defaultMethod = isset($options['default_method'])
            ? $options['default_method']
            : 'GET';

        // Set endpoint uri
        $this->baseUri = isset($options['base_uri'])
            ? $options['base_uri']
            : $this->baseUri;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD)
     *   Suppress warnings for PHPMD for now. It exceeds cyclomatic threshold but not sure how to improve it.
     */
    public function handle(RequestInterface $request)
    {
        $options = [];
        $body = $request->getBody();

        if ($request instanceof SortInterface) {
            $sort = $request->getSort();
            if (!empty($sort)) {
                foreach ($sort as $field => $order) {
                    $body['sort'][] = [
                        'field' => $field,
                        'order' => $order,
                    ];
                }
            }
        }

        if ($request instanceof SizeInterface) {
            $size = $request->getSize();
            if (!empty($size)) {
                $body['size'] = $size;
            }
        }

        if ($request instanceof OffsetInterface) {
            $offset = $request->getOffset();
            if (!empty($offset)) {
                $body['offset'] = $offset;
            }
        }

        // Always request application/json
        $headers = ['Accept' => 'application/json'];

        if (!empty($body)) {
            $body = json_encode($body);
        }

        if ($this->provider) {
            // Set the body.
            if (!empty($body)) {
                $options['body'] = $body;
            }
            // Set the headers.
            $options['headers'] = $headers;

            $psrRequest = $this->provider->getAuthenticatedRequest(
                $request->getMethod() ?: $this->defaultMethod,
                $this->baseUri . $request->getEndpoint(),
                $this->accessToken,
                $options
            );
            return $this->httpClient->sendRequest($psrRequest);
        }

        // If no provider was given. execute the request directly.
        $psrRequest = new Request(
            $request->getMethod() ?: $this->defaultMethod,
            $this->baseUri . $request->getEndpoint(),
            $headers,
            $body
        );
        return $this->httpClient->sendRequest($psrRequest);
    }
}

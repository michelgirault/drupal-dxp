<?php

namespace Dropsolid\UnomiSdkPhp\Http\ApiClient;

use Dropsolid\UnomiSdkPhp\Request\RequestInterface;
use Http\Client\Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ApiClientInterface
 *
 * @package Dropsolid\UnomiSdkPhp\Http
 */
interface ApiClientInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(RequestInterface $request);
}

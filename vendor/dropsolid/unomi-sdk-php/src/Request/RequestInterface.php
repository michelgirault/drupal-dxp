<?php

namespace Dropsolid\UnomiSdkPhp\Request;

/**
 * Interface RequestInterface
 *
 * @package Dropsolid\UnomiSdkPhp\Request
 */
interface RequestInterface
{
    /**
     * @return string|null
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @return array
     */
    public function getBody();
}

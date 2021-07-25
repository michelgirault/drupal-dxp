<?php

namespace Dropsolid\UnomiSdkPhp\Request;

/**
 * Interface SupportsMultipleMethods
 *
 * Used to indicate requests that support multiple methods.
 *
 * @package Dropsolid\UnomiSdkPhp\Request
 */
interface SupportsMultipleMethods
{
    /**
     * @param string $method
     * @return void
     */
    public function setMethod($method);
}

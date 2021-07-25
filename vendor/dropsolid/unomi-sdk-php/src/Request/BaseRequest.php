<?php

namespace Dropsolid\UnomiSdkPhp\Request;

/**
 * Class BaseRequest
 *
 * @package Dropsolid\UnomiSdkPhp\Request
 */
abstract class BaseRequest implements RequestInterface
{
    /**
     * @var array
     */
    protected $body = [];

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'GET';
    }
}

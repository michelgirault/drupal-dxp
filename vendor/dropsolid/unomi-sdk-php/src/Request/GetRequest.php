<?php

namespace Dropsolid\UnomiSdkPhp\Request;

/**
 * Class GetRequest
 *
 * @package Dropsolid\UnomiSdkPhp\Request
 */
abstract class GetRequest extends BaseRequest
{
    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'GET';
    }
}

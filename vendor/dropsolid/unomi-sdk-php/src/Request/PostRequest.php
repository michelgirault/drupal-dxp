<?php

namespace Dropsolid\UnomiSdkPhp\Request;

/**
 * Class PostRequest
 *
 * @package Dropsolid\UnomiSdkPhp\Request
 */
abstract class PostRequest extends BaseRequest
{
    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'POST';
    }
}

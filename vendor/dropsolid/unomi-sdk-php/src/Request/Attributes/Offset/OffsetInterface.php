<?php

namespace Dropsolid\UnomiSdkPhp\Request\Attributes\Offset;

/**
 * Interface OffsetInterface
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Attributes\Offset
 */
interface OffsetInterface
{
    /**
     * @param array $offset
     */
    public function setOffset(array $offset = []);

    /**
     * @return array
     */
    public function getOffset();
}

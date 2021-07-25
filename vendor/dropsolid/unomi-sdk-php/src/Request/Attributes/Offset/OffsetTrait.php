<?php

namespace Dropsolid\UnomiSdkPhp\Request\Attributes\Offset;

/**
 * Trait OffsetTrait
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Attributes\Offset
 */
trait OffsetTrait
{
    /**
     * @var array
     */
    protected $offset;

    /**
     * @return array
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param array $offset
     */
    public function setOffset(array $offset = [])
    {
        $this->offset = $offset;
    }
}

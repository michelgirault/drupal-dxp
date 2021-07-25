<?php

namespace Dropsolid\UnomiSdkPhp\Request\Attributes\Size;

/**
 * Trait SizeTrait
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Attributes\Size
 */
trait SizeTrait
{
    /**
     * @var array
     */
    protected $size;

    /**
     * @return array
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param array $size
     */
    public function setSize(array $size = [])
    {
        $this->size = $size;
    }
}

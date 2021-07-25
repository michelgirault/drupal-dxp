<?php

namespace Dropsolid\UnomiSdkPhp\Request\Attributes\Size;

/**
 * Interface SizeInterface
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Attributes\Size
 */
interface SizeInterface
{
    /**
     * @param array $size
     */
    public function setSize(array $size = []);

    /**
     * @return array
     */
    public function getSize();
}

<?php

namespace Dropsolid\UnomiSdkPhp\Request\Attributes\Sort;

/**
 * Interface SortInterface
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Attributes\Sort
 */
interface SortInterface
{
    /**
     * @param array $sort
     */
    public function setSort(array $sort = []);

    /**
     * @return array
     */
    public function getSort();
}

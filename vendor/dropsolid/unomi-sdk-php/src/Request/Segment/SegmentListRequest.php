<?php

namespace Dropsolid\UnomiSdkPhp\Request\Segment;

use Dropsolid\UnomiSdkPhp\Request\Attributes\Offset\OffsetInterface;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Offset\OffsetTrait;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Size\SizeInterface;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Size\SizeTrait;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Sort\SortInterface;
use Dropsolid\UnomiSdkPhp\Request\Attributes\Sort\SortTrait;
use Dropsolid\UnomiSdkPhp\Request\GetRequest;
use Dropsolid\UnomiSdkPhp\Request\MultipleMethodsTrait;

/**
 * Class SegmentListRequest
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Segment
 */
class SegmentListRequest extends GetRequest implements OffsetInterface, SizeInterface, SortInterface
{
    use MultipleMethodsTrait, OffsetTrait, SizeTrait, SortTrait;

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        return 'cxs/segments';
    }
}

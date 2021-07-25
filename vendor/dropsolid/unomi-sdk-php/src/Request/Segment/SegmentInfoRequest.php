<?php

namespace Dropsolid\UnomiSdkPhp\Request\Segment;

use Dropsolid\UnomiSdkPhp\Request\GetRequest;
use Dropsolid\UnomiSdkPhp\Request\MultipleMethodsTrait;

/**
 * Class SegmentInfoRequest
 *
 * @package Dropsolid\UnomiSdkPhp\Request\Segment
 */
class SegmentInfoRequest extends GetRequest
{
    use MultipleMethodsTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * SegmentInfoRequest constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        return 'cxs/segments/' . $this->id;
    }
}

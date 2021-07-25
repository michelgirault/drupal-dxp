<?php

namespace Dropsolid\UnomiSdkPhp\Repository;

use Dropsolid\UnomiSdkPhp\Model\Segment\Segment;
use Dropsolid\UnomiSdkPhp\Model\Segment\SegmentListView;
use Dropsolid\UnomiSdkPhp\Request\Segment\SegmentInfoRequest;
use Dropsolid\UnomiSdkPhp\Request\Segment\SegmentListRequest;
use Http\Client\Exception;

/**
 * Class SegmentRepository
 *
 * @package Dropsolid\UnomiSdkPhp\Repository
 */
class SegmentRepository extends RepositoryBase
{
    /**
     * @param string $id
     *
     * @return Segment
     * @throws Exception
     */
    public function getSegment($id)
    {
        $request = new SegmentInfoRequest($id);
        return $this->handleRequest(
            $request,
            Segment::class
        );
    }

    /**
     * @param array $offset
     * @param array $size
     * @param array $sort
     *
     * @return SegmentListView[]
     * @throws Exception
     */
    public function listSegments(array $offset = [], array $size = [], array $sort = [])
    {
        $request = new SegmentListRequest();
        $request->setOffset($offset);
        $request->setSize($size);
        $request->setSort($sort);

        return $this->handleRequest(
            $request,
            SegmentListView::class . '[]'
        );
    }
}

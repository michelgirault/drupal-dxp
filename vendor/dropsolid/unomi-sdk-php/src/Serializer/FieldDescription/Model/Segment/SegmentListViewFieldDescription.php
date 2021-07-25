<?php

namespace Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment;

use Dropsolid\UnomiSdkPhp\Model\Segment\SegmentListView;

/**
 * Class SegmentListViewFieldDescription
 *
 * @package Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment
 */
class SegmentListViewFieldDescription extends SegmentListViewFieldDescriptionBase
{
    /**
     * @inheritdoc
     */
    public function getTargetClass()
    {
        return SegmentListView::class;
    }

    /**
     * @inheritdoc
     */
    protected function getFieldMapping()
    {
        $fields = [
        ];

        return array_merge(
            parent::getFieldMapping(),
            $fields
        );
    }
}

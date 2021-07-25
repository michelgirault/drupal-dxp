<?php

namespace Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment;

use Dropsolid\UnomiSdkPhp\Model\Segment\Segment;

/**
 * Class SegmentFieldDescription
 *
 * @package Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment
 */
class SegmentFieldDescription extends SegmentFieldDescriptionBase
{
    /**
     * @inheritdoc
     */
    public function getTargetClass()
    {
        return Segment::class;
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

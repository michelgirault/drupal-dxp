<?php

namespace Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment;

use Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\FieldDescriptionBase;

/**
 * Class SegmentFieldDescriptionBase
 *
 * @package Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment
 */
abstract class SegmentListViewFieldDescriptionBase extends FieldDescriptionBase
{
    /**
     * @inheritdoc
     */
    protected function getFieldMapping()
    {
        return [
            'id',
            'name',
        ];
    }
}

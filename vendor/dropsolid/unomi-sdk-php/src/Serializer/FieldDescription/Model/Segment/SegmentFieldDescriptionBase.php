<?php

namespace Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment;

use Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\FieldDescriptionBase;

/**
 * Class SegmentFieldDescriptionBase
 *
 * @package Dropsolid\UnomiSdkPhp\Serializer\FieldDescription\Model\Segment
 */
abstract class SegmentFieldDescriptionBase extends FieldDescriptionBase
{
    /**
     * @inheritdoc
     */
    protected function getFieldMapping()
    {
        return [
            'itemId',
            'itemType',
            'version',
            'condition',
            'metadata',
        ];
    }
}

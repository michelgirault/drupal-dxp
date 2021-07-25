<?php

namespace Dropsolid\UnomiSdkPhp\Serializer\FieldDescription;

/**
 * Interface FieldDescriptionInterface
 *
 * @package Dropsolid\UnomiSdkPhp\Serializer\FieldDescription
 */
interface FieldDescriptionInterface
{
    /**
     * @return Field[]
     */
    public function getFields();

    /**
     * @return string
     */
    public function getTargetClass();
}

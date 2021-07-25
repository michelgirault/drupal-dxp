<?php

namespace Dropsolid\UnomiSdkPhp\Model\Segment;

/**
 * Class Segment
 *
 * @package Dropsolid\UnomiSdkPhp\Model\Segment
 */
class Segment
{
    /**
     * @var string
     */
    private $itemId;

    /**
     * @var string
     */
    private $itemType;

    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     * @todo parse this to an actual condition object
     */
    private $condition;

    /**
     * @var SegmentMetadata
     */
    private $metadata;

    /**
     * @return string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param string $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @param string $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return SegmentMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param SegmentMetadata $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }
}

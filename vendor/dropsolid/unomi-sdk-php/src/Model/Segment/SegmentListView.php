<?php

namespace Dropsolid\UnomiSdkPhp\Model\Segment;

/**
 * Class SegmentListView
 *
 * @package Dropsolid\UnomiSdkPhp\Model\Segment
 */
class SegmentListView
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $missingPlugins;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $id;

    /**
     * @var boolean
     */
    private $hidden;

    /**
     * @var boolean
     */
    private $readOnly;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var array
     */
    private $systemTags;


    /**
     * @param string $name
     */
    public function create($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isMissingPlugins()
    {
        return $this->missingPlugins;
    }

    /**
     * @param bool $missingPlugins
     */
    public function setMissingPlugins($missingPlugins)
    {
        $this->missingPlugins = $missingPlugins;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getSystemTags()
    {
        return $this->systemTags;
    }

    /**
     * @param array $systemTags
     */
    public function setSystemTags($systemTags)
    {
        $this->systemTags = $systemTags;
    }
}

<?php

namespace Basho\Riak\Core\Query;

/**
 * Represents the data and metadata stored in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakObject
{
    /**
     * The default content type assigned when storing in Riak if one is not
     */
    const DEFAULT_CONTENT_TYPE = "application/octet-stream";

    private $value;
    private $contentType;
    private $vtag;
    private $isDeleted;
    private $isModified;
    private $vClock;
    private $lastModified;
    private $indexes = [];
    private $links = [];
    private $meta = [];

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getVtag()
    {
        return $this->vtag;
    }

    /**
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @return boolean
     */
    public function getIsModified()
    {
        return $this->isModified;
    }

    /**
     * @return string
     */
    public function getVClock()
    {
        return $this->vClock;
    }

    /**
     * @return string
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param array $indexes
     */
    public function setIndexes(array $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @param array $meta
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param string $vtag
     */
    public function setVtag($vtag)
    {
        $this->vtag = $vtag;
    }

    /**
     * @param boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @param boolean $isModified
     */
    public function setIsModified($isModified)
    {
        $this->isModified = $isModified;
    }

    /**
     * @param string $vClock
     */
    public function setVClock($vClock)
    {
        $this->vClock = $vClock;
    }

    /**
     * @param string $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }
}

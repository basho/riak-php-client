<?php

namespace Basho\Riak\Core\Query;

use Basho\Riak\Core\Query\Index\RiakIndexList;
use Basho\Riak\Core\Query\Link\RiakLinkList;
use Basho\Riak\Core\Query\Meta\RiakUsermeta;

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

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $vtag;

    /**
     * @var boolean
     */
    private $isDeleted;

    /**
     * @var boolean
     */
    private $isModified;

    /**
     * @var \Basho\Riak\Cap\VClock
     */
    private $vClock;

    /**
     * @var string
     */
    private $lastModified;

    /**
     * @var \Basho\Riak\Core\Query\Index\RiakIndexList
     */
    private $indexes;

    /**
     * @var \Basho\Riak\Core\Query\Link\RiakLinkList
     */
    private $links;

    /**
     * @var \Basho\Riak\Core\Query\Meta\RiakUsermeta
     */
    private $meta;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \Basho\Riak\Core\Query\Index\RiakIndexList
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return \Basho\Riak\Core\Query\Link\RiakLinkList
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return \Basho\Riak\Core\Query\Meta\RiakUsermeta
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
     * @param \Basho\Riak\Core\Query\Index\RiakIndexList $indexes
     */
    public function setIndexes(RiakIndexList $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @param \Basho\Riak\Core\Query\Link\RiakLinkList $links
     */
    public function setLinks(RiakLinkList $links)
    {
        $this->links = $links;
    }

    /**
     * @param \Basho\Riak\Core\Query\Meta\RiakUsermeta $meta
     */
    public function setMeta(RiakUsermeta $meta)
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

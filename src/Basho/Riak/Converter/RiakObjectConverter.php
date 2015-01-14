<?php

namespace Basho\Riak\Converter;

use Basho\Riak\Cap\VClock;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Message\Kv\Content;
use Basho\Riak\Core\Query\RiakObjectList;
use Basho\Riak\Core\Query\Index\RiakIndex;
use Basho\Riak\Core\Query\Index\RiakIndexList;

/**
 * Riak object convertert.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakObjectConverter
{
    /**
     * @param array  $siblingsList
     * @param string $vClockString
     *
     * @return \Basho\Riak\Core\Query\RiakObjectList
     */
    public function convertToRiakObjectList(array $siblingsList, $vClockString)
    {
        $list   = [];
        $vClock = new VClock($vClockString);

        foreach ($siblingsList as $content) {
            $list[] = $this->convertToRiakObject($content, $vClock);
        }

        return new RiakObjectList($list);
    }

    /**
     * @param \Basho\Riak\Core\Message\Kv\Content $content
     * @param \Basho\Riak\Cap\VClock              $vClock
     *
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    private function convertToRiakObject(Content $content, VClock $vClock)
    {
        $object = new RiakObject();

        $object->setVClock($vClock);
        $object->setVtag($content->vtag);
        $object->setValue($content->value);
        $object->setContentType($content->contentType);
        $object->setIsDeleted((bool) $content->deleted);
        $object->setLastModified($content->lastModified);

        if ($content->indexes) {
            $object->setIndexes($this->createRiakIndexList($content->indexes));
        }

        // links;
        // meta;

        return $object;
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakObject $riakObject
     *
     * @return array
     */
    public function convertToRiakContent(RiakObject $riakObject)
    {
        $content = new Content();
        $indexes = $riakObject->getIndexes();

        $content->contentType  = $riakObject->getContentType() ?: RiakObject::DEFAULT_CONTENT_TYPE;
        $content->lastModified = $riakObject->getLastModified();
        $content->isDeleted    = $riakObject->getIsDeleted();
        $content->value        = $riakObject->getValue();
        $content->vtag         = $riakObject->getVtag();
        $content->indexes      = [];

        if ($indexes != null) {
            $content->indexes = $indexes->toArray();
        }

        return $content;
    }

    /**
     * @param array $indexes
     *
     * @return \Basho\Riak\Core\Query\Index\RiakIndexList
     */
    private function createRiakIndexList(array $indexes)
    {
        $list = [];

        foreach ($indexes as $fullName => $values) {
            $index = RiakIndex::fromFullname($fullName, $values);
            $name  = $index->getName();

            $list[$name] = $index;
        }

        return new RiakIndexList($list);
    }
}

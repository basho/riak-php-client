<?php

namespace Basho\Riak\Converter;

use Basho\Riak\Cap\VClock;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakObjectList;

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

        foreach ($siblingsList as $map) {
            $list[] = $this->convertToRiakObject($map, $vClock);
        }

        return new RiakObjectList($list);
    }

    /**
     * @param array                  $map
     * @param \Basho\Riak\Cap\VClock $vClock
     *
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    private function convertToRiakObject(array $map, VClock $vClock)
    {
        $object = new RiakObject();

        $object->setVClock($vClock);

        if (isset($map['value'])) {
            $object->setValue($map['value']);
        }

        if (isset($map['contentType'])) {
            $object->setContentType($map['contentType']);
        }

        if (isset($map['vtag'])) {
            $object->setVtag($map['vtag']);
        }

        if (isset($map['isDeleted'])) {
            $object->setIsDeleted((bool) $map['isDeleted']);
        }

        if (isset($map['isModified'])) {
            $object->setIsModified((bool) $map['isModified']);
        }

        if (isset($map['lastModified'])) {
            $object->setLastModified($map['lastModified']);
        }

        // indexes;
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
        $content = [
            'contentType'  => $riakObject->getContentType() ?: RiakObject::DEFAULT_CONTENT_TYPE,
            'lastModified' => $riakObject->getLastModified(),
            'isModified'   => $riakObject->getIsModified(),
            'isDeleted'    => $riakObject->getIsDeleted(),
            'value'        => $riakObject->getValue(),
            'vtag'         => $riakObject->getVtag()
        ];

        return $content;
    }
}
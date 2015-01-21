<?php

namespace Basho\Riak\Converter\Hydrator;

use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;

/**
 * The Converter acts as a bridge between the core and the user level API.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class DomainHydrator
{
    /**
     * @var Basho\Riak\Converter\Hydrator\DomainMetadataReader
     */
    private $metadataReader;

    /**
     * @param \Basho\Riak\Converter\Hydrator\DomainMetadataReader $metadataReader
     */
    public function __construct(DomainMetadataReader $metadataReader)
    {
        $this->metadataReader = $metadataReader;
    }

    /**
     * @param object                              $domainObject
     * @param \Basho\Riak\Core\Query\RiakObject   $riakObject
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     */
    public function setDomainObjectValues($domainObject, RiakObject $riakObject, RiakLocation $location)
    {
        $className = get_class($domainObject);
        $metadata  = $this->metadataReader->getMetadataFor($className);

        if (($keyField = $metadata->getRiakKeyField())) {
            $this->setDomainObjectProperty($domainObject, $keyField, $location->getKey());
        }

        if (($bucketNameField = $metadata->getRiakBucketNameField())) {
            $bucketName = $location->getNamespace() ? $location->getNamespace()->getBucketName() : null;
            $this->setDomainObjectProperty($domainObject, $bucketNameField, $bucketName);
        }

        if (($bucketTypeField = $metadata->getRiakBucketTypeField())) {
            $bucketName = $location->getNamespace() ? $location->getNamespace()->getBucketType() : null;
            $this->setDomainObjectProperty($domainObject, $bucketTypeField, $bucketName);
        }

        if (($vClockField = $metadata->getRiakVClockField())) {
            $this->setDomainObjectProperty($domainObject, $vClockField, $riakObject->getVClock());
        }

        if (($lastModifiedField = $metadata->getRiakLastModifiedField())) {
            $this->setDomainObjectProperty($domainObject, $lastModifiedField, $riakObject->getLastModified());
        }

        if (($contentTypeField = $metadata->getRiakContentTypeField())) {
            $this->setDomainObjectProperty($domainObject, $contentTypeField, $riakObject->getContentType());
        }
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakObject   $riakObject
     * @param object                              $domainObject
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     */
    public function setRiakObjectValues(RiakObject $riakObject, $domainObject, RiakLocation $location)
    {
        $className = get_class($domainObject);
        $metadata  = $this->metadataReader->getMetadataFor($className);

        if (($vClockField = $metadata->getRiakVClockField())) {
            $riakObject->setVClock($this->getDomainObjectProperty($domainObject, $vClockField));
        }

        if (($lastModifiedField = $metadata->getRiakLastModifiedField())) {
            $riakObject->setLastModified($this->getDomainObjectProperty($domainObject, $lastModifiedField));
        }

        if (($contentTypeField = $metadata->getRiakContentTypeField())) {
            $riakObject->setContentType($this->getDomainObjectProperty($domainObject, $contentTypeField));
        }
    }

    /**
     * @param object $domainObject
     * @param string $property
     * @param mixed  $value
     */
    private function setDomainObjectProperty($domainObject, $property, $value)
    {
        call_user_func([$domainObject, 'set' . ucfirst($property)], $value);
    }

    /**
     * @param object $domainObject
     * @param string $property
     *
     * @return mixed
     */
    private function getDomainObjectProperty($domainObject, $property)
    {
        return call_user_func([$domainObject, 'get' . ucfirst($property)]);
    }
}

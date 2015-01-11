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
     * @param object        $domainObject
     * @param RiakObject    $riakObject
     * @param RiakLocation  $location
     */
    public function setDomainObjectValues($domainObject, RiakObject $riakObject, RiakLocation $location)
    {
        $className = get_class($domainObject);
        $metadata  = $this->metadataReader->getRiakPropertiesMapping($className);

        if (isset($metadata['key'])) {
            $this->setDomainObjectProperty($domainObject, $metadata['key'], $location->getKey());
        }

        if (isset($metadata['bucketName'])) {
            $bucketName = $location->getNamespace() ? $location->getNamespace()->getBucketName() : null;
            $this->setDomainObjectProperty($domainObject, $metadata['bucketName'], $bucketName);
        }

        if (isset($metadata['bucketType'])) {
            $bucketName = $location->getNamespace() ? $location->getNamespace()->getBucketType() : null;
            $this->setDomainObjectProperty($domainObject, $metadata['bucketType'], $bucketName);
        }

        if (isset($metadata['vClock'])) {
            $this->setDomainObjectProperty($domainObject, $metadata['vClock'], $riakObject->getVClock());
        }

        if (isset($metadata['lastModified'])) {
            $this->setDomainObjectProperty($domainObject, $metadata['lastModified'], $riakObject->getLastModified());
        }

        if (isset($metadata['contentType'])) {
            $this->setDomainObjectProperty($domainObject, $metadata['contentType'], $riakObject->getContentType());
        }
    }

    /**
     * @param RiakObject    $riakObject
     * @param mixed         $domainObject
     * @param RiakLocation  $location
     */
    public function setRiakObjectValues(RiakObject $riakObject, $domainObject, RiakLocation $location)
    {
        if ( ! is_object($domainObject)) {
            $riakObject->setValue($domainObject);
        }

        $className = get_class($domainObject);
        $metadata  = $this->metadataReader->getRiakPropertiesMapping($className);

        if (isset($metadata['vClock'])) {
            $riakObject->setVClock($this->getDomainObjectProperty($domainObject, $metadata['vClock']));
        }

        if (isset($metadata['lastModified'])) {
            $riakObject->setLastModified($this->getDomainObjectProperty($domainObject, $metadata['lastModified']));
        }

        if (isset($metadata['contentType'])) {
            $riakObject->setContentType($this->getDomainObjectProperty($domainObject, $metadata['contentType']));
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

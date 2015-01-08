<?php

namespace Basho\Riak\Core\Converter;

use Basho\Riak\Core\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Core\Query\RiakObject;

/**
 * The Converter acts as a bridge between the core and the user level API.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseConverter implements Converter
{
    /**
     * @var \Basho\Riak\Core\Converter\Hydrator\DomainHydrator
     */
    private $domainHydrator;

    /**
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainHydrator $domainHydrator
     */
    public function __construct(DomainHydrator $domainHydrator)
    {
        $this->domainHydrator = $domainHydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDomain(DomainObjectReference $reference)
    {
        $riakObject      = new RiakObject();
        $location        = $reference->getLocation();
        $domainObject    = $reference->getDomainObject();
        $riakObjectValue = $this->fromDomainObject($domainObject);

        $riakObject->setValue($riakObjectValue);

        $this->domainHydrator->setRiakObjectValues($riakObject, $domainObject, $location);

        return $riakObject;
    }

    /**
     * {@inheritdoc}
     */
    public function toDomain(RiakObjectReference $reference)
    {
        $location     = $reference->getLocation();
        $riakObject   = $reference->getRiakObject();
        $type         = $reference->getDomainObjectType();
        $domainObject = $this->toDomainObject($riakObject->getValue(), $type);

        $this->domainHydrator->setDomainObjectValues($domainObject, $riakObject, $location);

        return $domainObject;
    }

    /**
     * Convert the value portion of a RiakObject to a domain object.
     *
     * @param string $value
     * @param string $type
     *
     * @return a new instance of the domain object
     */
    abstract protected function toDomainObject($value, $type);

    /**
     * Provide the value portion of a RiakObject from the domain object.
     *
     * @param mixed $domainObject
     *
     * @return string
     */
    abstract protected function fromDomainObject($domainObject);
}

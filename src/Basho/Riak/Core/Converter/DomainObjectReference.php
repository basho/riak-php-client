<?php

namespace Basho\Riak\Core\Converter;

use Basho\Riak\Core\Query\RiakLocation;

/**
 * Encapsulates a domain object.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DomainObjectReference
{
    /**
     * @var mixed
     */
    private $domainObject;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @param mixed        $domainObject
     * @param RiakLocation $location
     */
    public function __construct($domainObject, RiakLocation $location)
    {
        $this->domainObject = $domainObject;
        $this->location     = $location;
    }

    /**
     * @return mixed
     */
    public function getDomainObject()
    {
        return $this->domainObject;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }
}

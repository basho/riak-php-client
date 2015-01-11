<?php

namespace Basho\Riak\Converter;

use Basho\Riak\Core\Query\RiakLocation;

/**
 * Encapsulates a domain object.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

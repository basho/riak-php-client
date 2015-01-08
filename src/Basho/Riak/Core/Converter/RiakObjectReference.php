<?php

namespace Basho\Riak\Core\Converter;

use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;

/**
 * Encapsulates a Riak object.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakObjectReference
{
    /**
     * @var \Basho\Riak\Core\Query\RiakObject
     */
    private $riakObject;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var string
     */
    private $domainObjectType;

    /**
     * @param \Basho\Riak\Core\Query\RiakObject   $riakObject
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param string                              $type
     */
    public function __construct(RiakObject $riakObject, RiakLocation $location, $type = null)
    {
        $this->riakObject       = $riakObject;
        $this->location         = $location;
        $this->domainObjectType = $type;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    public function getRiakObject()
    {
        return $this->riakObject;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getDomainObjectType()
    {
        return $this->domainObjectType;
    }
}

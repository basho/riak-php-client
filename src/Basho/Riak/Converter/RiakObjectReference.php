<?php

namespace Basho\Riak\Converter;

use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;

/**
 * Encapsulates a Riak object.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

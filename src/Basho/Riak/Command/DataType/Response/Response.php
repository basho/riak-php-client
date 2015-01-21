<?php

namespace Basho\Riak\Command\DataType\Response;

use Basho\Riak\RiakResponse;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\DataType;

/**
 * Base Response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Core\Query\Crdt\DataType
     */
    private $datatype;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation  $location
     * @param \Basho\Riak\Core\Query\Crdt\DataType $datatype
     */
    public function __construct(RiakLocation $location = null, DataType $datatype = null)
    {
        $this->datatype = $datatype;
        $this->location = $location;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get the datatype from this response.
     *
     * @return \Basho\Riak\Core\Query\Crdt\DataType
     */
    public function getDatatype()
    {
        return $this->datatype;
    }
}

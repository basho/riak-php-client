<?php

namespace Basho\Riak\Command\DataType\Response;

use Basho\Riak\RiakResponse;
use Basho\Riak\Core\Query\Crdt\DataType;

/**
 * Base Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Basho\Riak\Core\Query\Crdt\DataType
     */
    private $datatype;

    /**
     * @param \Basho\Riak\Core\Query\Crdt\DataType $datatype
     */
    public function __construct(DataType $datatype)
    {
        $this->datatype = $datatype;
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

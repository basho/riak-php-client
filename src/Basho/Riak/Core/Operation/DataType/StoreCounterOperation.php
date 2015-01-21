<?php

namespace Basho\Riak\Core\Operation\DataType;

use Basho\Riak\Command\DataType\Response\StoreCounterResponse;
use Basho\Riak\Core\Query\Crdt\DataType;

/**
 * An operation used to store a counter from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreCounterOperation extends StoreDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null)
    {
        return new StoreCounterResponse($this->location, $datatype);
    }
}

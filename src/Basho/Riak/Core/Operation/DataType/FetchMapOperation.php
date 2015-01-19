<?php

namespace Basho\Riak\Core\Operation\DataType;

use Basho\Riak\Command\DataType\Response\FetchMapResponse;
use Basho\Riak\Core\Query\Crdt\DataType;

/**
 * An operation used to fetch a map from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchMapOperation extends FetchDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null)
    {
        return new FetchMapResponse($this->location, $datatype);
    }
}

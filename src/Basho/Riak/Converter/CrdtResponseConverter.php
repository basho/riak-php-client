<?php

namespace Basho\Riak\Converter;

use Basho\Riak\Core\Message\DataType\Response;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Core\Query\Crdt\RiakSet;
use Basho\Riak\RiakException;

/**
 * Crdt response converter
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class CrdtResponseConverter
{
    /**
     * @param \Basho\Riak\Core\Message\DataType\Response $response
     *
     * @return \Basho\Riak\Core\Query\Crdt\DataType;
     */
    public function convert(Response $response)
    {
        if ($response->type == null) {
            return;
        }

        if ($response->type == 'counter') {
            return $this->convertCounter($response);
        }

        if ($response->type == 'set') {
            return $this->convertSet($response);
        }

        throw new RiakException("Unknown crdt type : {$response->type}");
    }

    /**
     * @param \Basho\Riak\Core\Message\DataType\Response $response
     *
     * @return \Basho\Riak\Core\Query\Crdt\RiakCounter
     */
    public function convertCounter(Response $response)
    {
        $value   = $response->value;
        $counter = new RiakCounter($value);

        return $counter;
    }

    /**
     * @param \Basho\Riak\Core\Message\DataType\Response $response
     *
     * @return \Basho\Riak\Core\Query\Crdt\RiakSet
     */
    public function convertSet(Response $response)
    {
        $value = $response->value;
        $set   = new RiakSet($value);

        return $set;
    }
}

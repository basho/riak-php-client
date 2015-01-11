<?php

namespace Basho\Riak\Converter;

use Basho\Riak\Core\Message\DataType\Response;
use Basho\Riak\Core\Query\Crdt\RiakCounter;

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
    public function convertCounter(Response $response)
    {
        $value   = $response->value;
        $counter = new RiakCounter($value);

        return $counter;
    }
}

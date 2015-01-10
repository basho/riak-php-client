<?php

namespace Basho\Riak\Command\DataType\Response;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakCounter;

/**
 * Store counter response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreCounterResponse extends Response
{
    /**
     * @param \Basho\Riak\Core\Query\RiakLocation     $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter $datatype
     */
    public function __construct(RiakLocation $location = null, RiakCounter $datatype = null)
    {
        parent::__construct($location, $datatype);
    }
}

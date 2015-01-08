<?php

namespace Basho\Riak\Core;

use Basho\Riak\Core\RiakAdapter;

/**
 * Riak Operation
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakOperation
{
    /**
     * Execute the operation.
     *
     * @param Basho\Riak\Core\RiakAdapter $adapter
     *
     * @return \Basho\Riak\RiakResponse
     */
    public function execute(RiakAdapter $adapter);
}

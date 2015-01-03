<?php

namespace Basho\Riak;

use Basho\Riak\Core\RiakCluster;

/**
 * The base class for all Riak Commands.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakCommand
{
    /**
     * @param \Basho\Riak\Core\RiakCluster $cluster
     *
     * @return \Basho\Riak\RiakResponse
     */
    public function execute(RiakCluster $cluster);
}

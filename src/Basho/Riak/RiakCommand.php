<?php

namespace Basho\Riak;

use Basho\Riak\Core\RiakCluster;

/**
 * The base class for all Riak Commands.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

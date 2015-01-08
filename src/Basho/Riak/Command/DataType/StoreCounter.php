<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Command\Kv\RiakLocation;
use Basho\Riak\Command\DataType\Builder\StoreCounterBuilder;

/**
 * Command used to update or create a counter datatype in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @author Christopher Mancini <cmancini@basho.com>
 */
class StoreCounter implements RiakCommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        throw new RiakException("Not implemented");
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreCounterBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new StoreCounterBuilder($location);
    }
}

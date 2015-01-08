<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Command\Kv\RiakLocation;
use Basho\Riak\Command\DataType\Builder\StoreSetBuilder;

/**
 * Command used to update or create a set datatype in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @author Christopher Mancini <cmancini@basho.com>
 */
class StoreSet implements RiakCommand
{
    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function add($value)
    {
        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function remove($value)
    {
        return $this;
    }

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
     * @return \Basho\Riak\Command\DataType\Builder\StoreSetBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new StoreSetBuilder($location);
    }
}

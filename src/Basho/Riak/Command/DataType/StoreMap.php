<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Command\Kv\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakSet;
use Basho\Riak\Core\Query\Crdt\RiakMap;
use Basho\Riak\Core\Query\Crdt\RiakFlag;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Core\Query\Crdt\RiakRegister;
use Basho\Riak\Command\DataType\Builder\StoreMapBuilder;

/**
 * Command used to update or create a map datatype in Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreMap implements RiakCommand
{
    /**
     * Update the map in Riak by removing the counter mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function removeCounter($key)
    {
        return $this;
    }

    /**
     * Update the map in Riak by removing the register mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function removeRegister($key)
    {
        return $this;
    }

    /**
     * Update the map in Riak by removing the flag mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function removeFlag($key)
    {
        return $this;
    }

    /**
     * Update the map in Riak by removing the set mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function removeSet($key)
    {
        return $this;
    }

    /**
     * Update the map in Riak by removing the map mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function removeMap($key)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the map mapped to the provided key.
     *
     * @param string                               $key
     * @param \Basho\Riak\Core\Query\Crdt\RiakMap  $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateMap($key, RiakMap $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the set mapped to the provided key.
     *
     * @param string                               $key
     * @param \Basho\Riak\Core\Query\Crdt\RiakSet  $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateSet($key, RiakSet $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the counter mapped to the provided key.
     *
     * @param string                                  $key
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateCounter($key, RiakCounter $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the register mapped to the provided key.
     *
     * @param string                                   $key
     * @param \Basho\Riak\Core\Query\Crdt\RiakRegister $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateRegister($key, RiakRegister $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the flag mapped to the provided key.
     *
     * @param string                               $key
     * @param \Basho\Riak\Core\Query\Crdt\RiakFlag $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateFlag($key, RiakFlag $value)
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
     * @return \Basho\Riak\Command\DataType\Builder\StoreMapBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new StoreMapBuilder($location);
    }
}

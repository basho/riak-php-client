<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
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
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param array                               $options
     */
    public function __construct(RiakLocation $location = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
    }

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
     * @param string $key
     * @param array  $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateMap($key, $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the set mapped to the provided key.
     *
     * @param string $key
     * @param array  $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateSet($key, $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the counter mapped to the provided key.
     *
     * @param string  $key
     * @param integer $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateCounter($key, $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the register mapped to the provided key.
     *
     * @param string $key
     * @param string $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateRegister($key, $value)
    {
        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the flag mapped to the provided key.
     *
     * @param string  $key
     * @param boolean $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateFlag($key, $value)
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
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param array                               $options
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreMapBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreMapBuilder($location, $options);
    }
}

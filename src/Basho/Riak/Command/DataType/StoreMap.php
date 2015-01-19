<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\Op\MapOp;
use Basho\Riak\Core\Query\Crdt\Op\FlagOp;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;
use Basho\Riak\Core\Query\Crdt\Op\RegisterOp;
use Basho\Riak\Command\DataType\Builder\StoreMapBuilder;
use Basho\Riak\Core\Operation\DataType\StoreMapOperation;

/**
 * Command used to update or create a map datatype in Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreMap extends StoreDataType
{
    /**
     * @var \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    private $op;

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        if ($this->op === null) {
            $this->op = new MapOp();
        }

        return $this->op;
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
        $this->getOp()->removeCounter($key);

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
        $this->getOp()->removeRegister($key);

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
        $this->getOp()->removeFlag($key);

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
        $this->getOp()->removeSet($key);

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
        $this->getOp()->removeMap($key);

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the map mapped to the provided key.
     *
     * @param string                                $key
     * @param \Basho\Riak\Command\DataType\StoreMap $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateMap($key, StoreMap $value)
    {
        $this->getOp()->updateMap($key, $value->getOp());

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the set mapped to the provided key.
     *
     * @param string                                $key
     * @param \Basho\Riak\Command\DataType\StoreSet $value
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function updateSet($key, StoreSet $value)
    {
        $this->getOp()->updateSet($key, $value->getOp());

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
        $this->getOp()->updateCounter($key, new CounterOp($value));

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
        $this->getOp()->updateRegister($key, new RegisterOp($value));

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
        $this->getOp()->updateFlag($key, new FlagOp($value));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreMapOperation($converter, $this->location, $this->getOp(), $this->options);
        $response  = $cluster->execute($operation);

        return $response;
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

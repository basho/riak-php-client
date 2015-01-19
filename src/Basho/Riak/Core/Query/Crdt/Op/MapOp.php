<?php

namespace Basho\Riak\Core\Query\Crdt\Op;

/**
 * Riak Map crdt op.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class MapOp implements CrdtOp
{
    /**
     * @var array
     */
    private $removes = [
        'register'  => [],
        'counter'   => [],
        'flag'      => [],
        'map'       => [],
        'set'       => [],
    ];

    /**
     * @var array
     */
    private $updates = [
        'register'  => [],
        'counter'   => [],
        'flag'      => [],
        'map'       => [],
        'set'       => [],
    ];

    /**
     * @return array
     */
    public function getRemoves()
    {
        return $this->removes;
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        return $this->updates;
    }

    /**
     * @param string $key
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function removeCounter($key)
    {
        $this->reomoveCrdt($key, 'counter');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function removeRegister($key)
    {
        $this->reomoveCrdt($key, 'register');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function removeFlag($key)
    {
        $this->reomoveCrdt($key, 'flag');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function removeSet($key)
    {
        $this->reomoveCrdt($key, 'set');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function removeMap($key)
    {
        $this->reomoveCrdt($key, 'map');

        return $this;
    }

    /**
     * @param string                               $key
     * @param \Basho\Riak\Core\Query\Crdt\Op\SetOp $op
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function updateSet($key, SetOp $op)
    {
        return $this->updateCrdt($key, 'set', $op);
    }

    /**
     * @param string                                   $key
     * @param \Basho\Riak\Core\Query\Crdt\Op\CounterOp $op
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function updateCounter($key, CounterOp $op)
    {
        return $this->updateCrdt($key, 'counter', $op);
    }

    /**
     * @param string                               $key
     * @param \Basho\Riak\Core\Query\Crdt\Op\MapOp $op
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function updateMap($key, MapOp $op)
    {
        return $this->updateCrdt($key, 'map', $op);
    }

    /**
     * @param string                                    $key
     * @param \Basho\Riak\Core\Query\Crdt\Op\RegisterOp $op
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function updateRegister($key, RegisterOp $op)
    {
        return $this->updateCrdt($key, 'register', $op);
    }

    /**
     * @param string                                $key
     * @param \Basho\Riak\Core\Query\Crdt\Op\FlagOp $op
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    public function updateFlag($key, FlagOp $op)
    {
        return $this->updateCrdt($key, 'flag', $op);
    }

    /**
     * @param string                                $key
     * @param string                                $type
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    private function updateCrdt($key, $type, CrdtOp $op)
    {
        $this->updates[$type][$key] = $op;

        return $this;
    }

    /**
     * @param string $key
     * @param string $type
     *
     * @return \Basho\Riak\Core\Query\Crdt\Op\MapOp
     */
    private function reomoveCrdt($key, $type)
    {
        $this->removes[$type] = $key;

        return $this;
    }
}

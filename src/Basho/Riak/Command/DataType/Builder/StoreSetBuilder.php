<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Core\Query\Crdt\DataType;
use Basho\Riak\Command\DataType\StoreSet;

/**
 * Used to construct a StoreSet command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreSetBuilder extends Builder
{
    /**
     * @var array
     */
    private $adds = [];

    /**
     * @var array
     */
    private $removes = [];

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function add($value)
    {
        $this->adds[] = $value;

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
        $this->removes[] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function build()
    {
        $command = new StoreSet($this->location, $this->options);

        array_walk($this->adds, [$command, 'add']);
        array_walk($this->removes, [$command, 'remove']);

        return $command;
    }
}

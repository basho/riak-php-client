<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Command\DataType\StoreCounter;

/**
 * Used to construct a StoreCounter command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreCounterBuilder extends Builder
{
    /**
     * @var integer
     */
    private $counter;

    /**
     * @param integer $delta
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreCounterBuilder
     */
    public function withDelta($delta)
    {
        $this->counter = $delta;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreCounter
     */
    public function build()
    {
        $command = new StoreCounter($this->location, $this->options);

        if ($this->counter != null) {
            $command->withDelta($this->counter);
        }

        return $command;
    }
}

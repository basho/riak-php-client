<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\StoreCounter;

/**
 * Used to construct a StoreCounter command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreCounterBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreCounter
     */
    public function build()
    {
        return new StoreCounter($this->location, $this->options);
    }
}

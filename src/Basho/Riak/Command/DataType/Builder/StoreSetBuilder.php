<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\StoreSet;

/**
 * Used to construct a StoreSet command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSetBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function build()
    {
        return new StoreSet($this->location, $this->options);
    }
}

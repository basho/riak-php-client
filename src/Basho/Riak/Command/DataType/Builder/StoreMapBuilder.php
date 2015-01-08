<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\StoreMap;

/**
 * Used to construct a StoreMap command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreMapBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function build()
    {
        return new StoreMap($this->location, $this->options);
    }
}

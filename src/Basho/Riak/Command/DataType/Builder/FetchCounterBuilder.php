<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\FetchCounter;

/**
 * Used to construct a FetchCounter command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchCounterBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\FetchCounter
     */
    public function build()
    {
        return new FetchCounter($this->location, $this->options);
    }
}

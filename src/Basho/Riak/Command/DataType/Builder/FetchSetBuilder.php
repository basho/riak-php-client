<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\FetchSet;

/**
 * Used to construct a FetchSet command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSetBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\FetchSet
     */
    public function build()
    {
        return new FetchSet($this->location, $this->options);
    }
}

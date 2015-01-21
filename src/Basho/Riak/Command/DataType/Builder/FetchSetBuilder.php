<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\FetchSet;

/**
 * Used to construct a FetchSet command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

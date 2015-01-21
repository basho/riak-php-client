<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Command\DataType\FetchMap;

/**
 * Used to construct a FetchMap command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchMapBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\FetchMap
     */
    public function build()
    {
        return new FetchMap($this->location, $this->options);
    }
}

<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakMap;
use Basho\Riak\Command\DataType\StoreMap;

/**
 * Used to construct a StoreMap command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreMapBuilder extends Builder
{
    /**
     * @var \Basho\Riak\Core\Query\Crdt\RiakMap
     */
    private $map;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakMap $map
     * @param array                               $options
     */
    public function __construct(RiakLocation $location = null, RiakMap $map = null, array $options = [])
    {
        parent::__construct($location, $options);

        $this->map = $map;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\FetchMap $map
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreMapBuilder
     */
    public function withMap(RiakMap $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreMap
     */
    public function build()
    {
        return new StoreMap($this->location, $this->map, $this->options);
    }
}

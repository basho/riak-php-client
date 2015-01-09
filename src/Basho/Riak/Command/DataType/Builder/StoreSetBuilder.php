<?php

namespace Basho\Riak\Command\DataType\Builder;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakSet;
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
     * @var \Basho\Riak\Core\Query\Crdt\RiakSet
     */
    private $set;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakSet $set
     * @param array                               $options
     */
    public function __construct(RiakLocation $location = null, RiakMap $set = null, array $options = [])
    {
        parent::__construct($location, $options);

        $this->set = $set;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\RiakSet $set
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreSetBuilder
     */
    public function withSet(RiakSet $set)
    {
        $this->set = $set;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function build()
    {
        return new StoreSet($this->location, $this->set, $this->options);
    }
}

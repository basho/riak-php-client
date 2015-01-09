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
     * @var \Basho\Riak\Core\Query\Crdt\RiakCounter
     */
    private $counter;

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter $counter
     * @param array                                   $options
     */
    public function __construct(RiakLocation $location = null, RiakCounter $counter = null, array $options = [])
    {
        parent::__construct($location, $options);

        $this->counter  = $counter;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter $counter
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreCounterBuilder
     */
    public function withCounter(RiakCounter $counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreCounter
     */
    public function build()
    {
        return new StoreCounter($this->location, $this->counter, $this->options);
    }
}

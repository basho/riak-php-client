<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Command\DataType\Builder\StoreCounterBuilder;
use Basho\Riak\Core\Operation\DataType\StoreCounterOperation;

/**
 * Command used to update or create a counter datatype in Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreCounter implements RiakCommand
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var \Basho\Riak\Core\Query\Crdt\RiakCounter
     */
    private $counter;

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter $counter
     * @param array                                   $options
     */
    public function __construct(RiakLocation $location, RiakCounter $counter, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->counter  = $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreCounterOperation($converter, $this->location, $this->counter, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter $counter
     * @param array                                   $options
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreCounterBuilder
     */
    public static function builder(RiakLocation $location = null, RiakCounter $counter = null, array $options = [])
    {
        return new StoreCounterBuilder($location, $counter, $options);
    }
}

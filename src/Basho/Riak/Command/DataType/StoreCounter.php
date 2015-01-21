<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;
use Basho\Riak\Command\DataType\Builder\StoreCounterBuilder;
use Basho\Riak\Core\Operation\DataType\StoreCounterOperation;

/**
 * Command used to update or create a counter datatype in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreCounter extends StoreDataType
{
    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param array                                   $options
     */
    public function __construct(RiakLocation $location, array $options = [])
    {
        parent::__construct($location, new CounterUpdate(), $options);
    }

    /**
     * @param integer $delta
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreCounterBuilder
     */
    public function withDelta($delta)
    {
        $this->update->withDelta($delta);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $op        = $this->update->getOp();
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreCounterOperation($converter, $this->location, $op, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param array                                   $options
     *
     * @return \Basho\Riak\Command\DataType\Builder\StoreCounterBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreCounterBuilder($location, $options);
    }
}

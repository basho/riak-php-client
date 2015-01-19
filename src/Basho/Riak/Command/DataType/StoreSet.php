<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\DataType;
use Basho\Riak\Core\Query\Crdt\Op\SetOp;
use Basho\Riak\Command\DataType\Builder\StoreSetBuilder;
use Basho\Riak\Core\Operation\DataType\StoreSetOperation;

/**
 * Command used to update or create a set datatype in Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreSet extends StoreDataType
{
    /**
     * @var array
     */
    private $adds = [];

    /**
     * @var array
     */
    private $removes = [];

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function add($value)
    {
        $this->adds[] = $value;

        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function remove($value)
    {
        $this->removes[] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreSetOperation($converter, $this->location, $this->getOp(), $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        return new SetOp($this->adds, $this->removes);
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param array                               $options
     *
     * @return \Basho\Riak\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreSetBuilder($location, $options);
    }
}

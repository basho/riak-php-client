<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Command\DataType\Builder\StoreSetBuilder;
use Basho\Riak\Core\Operation\DataType\StoreSetOperation;

/**
 * Command used to update or create a set datatype in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreSet extends StoreDataType
{
    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param array                                   $options
     */
    public function __construct(RiakLocation $location, array $options = [])
    {
        parent::__construct($location, new SetUpdate(), $options);
    }

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function add($value)
    {
        $this->update->add($value);

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
        $this->update->remove($value);

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
        $operation = new StoreSetOperation($converter, $this->location, $op, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
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

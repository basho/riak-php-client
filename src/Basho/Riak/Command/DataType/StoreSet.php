<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakSet;
use Basho\Riak\Core\Query\Crdt\DataType;
use Basho\Riak\Command\DataType\Builder\StoreSetBuilder;

/**
 * Command used to update or create a set datatype in Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreSet implements RiakCommand
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
     * @var \Basho\Riak\Core\Query\Crdt\RiakSet
     */
    private $set;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakSet $set
     * @param array                               $options
     */
    public function __construct(RiakLocation $location = null, RiakSet $set = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->set      = $set;
    }

    /**
     * Add the provided value to the set in Riak.
     *
     * @param \Basho\Riak\Core\Query\Crdt\DataType $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function add(DataType $value)
    {
        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param \Basho\Riak\Core\Query\Crdt\DataType $value
     *
     * @return \Basho\Riak\Command\DataType\StoreSet
     */
    public function remove(DataType $value)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        throw new RiakException("Not implemented");
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakSet $set
     * @param array                               $options
     *
     * @return \Basho\Riak\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null, RiakSet $set = null, array $options = [])
    {
        return new StoreSetBuilder($location, $set, $options);
    }
}

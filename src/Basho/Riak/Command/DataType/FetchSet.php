<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\Crdt\RiakSet;
use Basho\Riak\Command\DataType\Builder\FetchSetBuilder;

/**
 * Command used to fetch a set datatype from Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchSet implements RiakCommand
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
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     * @param array                               $options
     */
    public function __construct(RiakLocation $location = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
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
     * @param array                               $options
     *
     * @return \Basho\Riak\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new FetchSetBuilder($location, $options);
    }
}

<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Command\Kv\RiakLocation;
use Basho\Riak\Command\DataType\Builder\FetchSetBuilder;

/**
 * Command used to fetch a set datatype from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @author Christopher Mancini <cmancini@basho.com>
 */
class FetchSet implements RiakCommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        throw new RiakException("Not implemented");
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     *
     * @return \Basho\Riak\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new FetchSetBuilder($location);
    }
}
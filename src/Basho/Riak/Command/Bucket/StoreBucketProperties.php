<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\Bucket\Builder\StoreBucketPropertiesBuilder;

/**
 * Command used to store the properties of a bucket in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreBucketProperties implements RiakCommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        throw new RiakException("Not implemented");
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     *
     * @return \Basho\Riak\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public static function builder(RiakNamespace $namespace = null)
    {
        return new StoreBucketPropertiesBuilder($namespace);
    }
}
<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\Bucket\Builder\FetchBucketPropertiesBuilder;

/**
 * Command used to fetch the properties of a bucket in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchBucketProperties implements RiakCommand
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
     * @return \Basho\Riak\Command\Bucket\Builder\FetchBucketPropertiesBuilder
     */
    public static function builder(RiakNamespace $namespace = null)
    {
        return new FetchBucketPropertiesBuilder($namespace);
    }
}
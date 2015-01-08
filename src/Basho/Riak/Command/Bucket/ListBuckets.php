<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Command\Bucket\Builder\ListBucketsBuilder;

/**
 * Command used to list the buckets contained in a bucket type.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBuckets implements RiakCommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        throw new RiakException("Not implemented");
    }

    /**
     * @param string $type
     *
     * @return \Basho\Riak\Command\Bucket\Builder\ListBucketsBuilder
     */
    public static function builder($type = null)
    {
        return new ListBucketsBuilder($type);
    }
}

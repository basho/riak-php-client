<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\RiakCommand;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Command\Bucket\Builder\ListBucketsBuilder;

/**
 * Command used to list the buckets contained in a bucket type.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

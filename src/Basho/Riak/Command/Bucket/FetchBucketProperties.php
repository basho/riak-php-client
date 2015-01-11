<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\RiakException;
use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\Bucket\Builder\FetchBucketPropertiesBuilder;

/**
 * Command used to fetch the properties of a bucket in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

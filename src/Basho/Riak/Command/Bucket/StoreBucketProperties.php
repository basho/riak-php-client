<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Operation\Bucket\StorePropertiesOperation;
use Basho\Riak\Command\Bucket\Builder\StoreBucketPropertiesBuilder;

/**
 * Command used to store the properties of a bucket in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreBucketProperties implements RiakCommand
{
    /**
     * @var \Basho\Riak\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var array
     */
    private $properties;

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     * @param array                                $properties
     */
    public function __construct(RiakNamespace $namespace, array $properties)
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new StorePropertiesOperation($this->namespace, $this->properties);
        $response  = $cluster->execute($operation);

        return $response;
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

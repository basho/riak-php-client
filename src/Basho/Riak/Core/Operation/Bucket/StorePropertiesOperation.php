<?php

namespace Basho\Riak\Core\Operation\Bucket;

use Basho\Riak\Command\Bucket\Response\StoreBucketPropertiesResponse;
use Basho\Riak\Core\Message\Bucket\PutRequest;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;

/**
 * An operation used to store bucket properties in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StorePropertiesOperation implements RiakOperation
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
     * @param \Basho\Riak\Core\Query\RiakNamespace        $namespace
     * @param \Basho\Riak\Core\Query\RiakBucketProperties $properties
     */
    public function __construct(RiakNamespace $namespace, array $properties)
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $adapter->send($this->createGetRequest());

        return new StoreBucketPropertiesResponse($this->namespace);
    }

    /**
     * @return \Basho\Riak\Core\Message\Bucket\PutRequest
     */
    private function createGetRequest()
    {
        $request = new PutRequest();

        $request->type   = $this->namespace->getBucketType();
        $request->bucket = $this->namespace->getBucketName();

        foreach ($this->properties as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }
}

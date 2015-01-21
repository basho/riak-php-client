<?php

namespace Basho\Riak\Core\Operation\Bucket;

use Basho\Riak\Command\Bucket\Response\FetchBucketPropertiesResponse;
use Basho\Riak\Core\Message\Bucket\GetResponse;
use Basho\Riak\Core\Message\Bucket\GetRequest;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;

/**
 * An operation used to fetch bucket properties from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchPropertiesOperation implements RiakOperation
{
    /**
     * @var \Basho\Riak\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace)
    {
        $this->namespace  = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $getRequest  = $this->createGetRequest();
        $getResponse = $adapter->send($getRequest);

        $bucketProps = $this->createBucketProps($getResponse);
        $response    = new FetchBucketPropertiesResponse($this->namespace, $bucketProps);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\Bucket\GetRequest
     */
    private function createGetRequest()
    {
        $request = new GetRequest();

        $request->type   = $this->namespace->getBucketType();
        $request->bucket = $this->namespace->getBucketName();

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Message\Bucket\GetResponse $response
     *
     * @return \Basho\Riak\Core\Query\BucketProperties
     */
    private function createBucketProps(GetResponse $response)
    {
        $values = [];

        foreach ($response as $key => $value) {
            $values[$key] = $value;
        }

        return new BucketProperties($values);
    }
}

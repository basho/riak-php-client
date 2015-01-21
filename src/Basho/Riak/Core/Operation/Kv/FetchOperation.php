<?php

namespace Basho\Riak\Core\Operation\Kv;

use Basho\Riak\Command\Kv\Response\FetchValueResponse;
use Basho\Riak\Core\Message\Kv\GetRequest;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;
use Basho\Riak\RiakConfig;

/**
 * An operation used to fetch an object from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchOperation implements RiakOperation
{
    /**
     * @var \Basho\Riak\RiakConfig
     */
    private $config;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\RiakConfig                    $config
     * @param \Basho\Riak\Core\Query\RiakLocation       $location
     * @param array                                     $options
     */
    public function __construct(RiakConfig $config, RiakLocation $location, array $options)
    {
        $this->location = $location;
        $this->options  = $options;
        $this->config   = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $getRequest       = $this->createGetRequest();
        $getResponse      = $adapter->send($getRequest);
        $resolverFactory  = $this->config->getResolverFactory();
        $converterFactory = $this->config->getConverterFactory();
        $objectConverter  = $this->config->getRiakObjectConverter();

        $vClock      = $getResponse->vClock;
        $unchanged   = $getResponse->unchanged;
        $contentList = $getResponse->contentList;
        $notFound    = empty($getResponse->contentList);
        $objectList  = $objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new FetchValueResponse($converterFactory, $resolverFactory, $this->location, $objectList);

        $response->setNotFound($notFound);
        $response->setUnchanged($unchanged);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\Kv\GetRequest
     */
    private function createGetRequest()
    {
        $request   = new GetRequest();
        $namespace = $this->location->getNamespace();

        $request->type   = $namespace->getBucketType();
        $request->bucket = $namespace->getBucketName();
        $request->key    = $this->location->getKey();

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }
}

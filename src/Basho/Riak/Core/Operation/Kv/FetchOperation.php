<?php

namespace Basho\Riak\Core\Operation\Kv;

use Basho\Riak\Command\Kv\Response\FetchValueResponse;
use Basho\Riak\Converter\RiakObjectConverter;
use Basho\Riak\Converter\ConverterFactory;
use Basho\Riak\Core\Message\Kv\GetRequest;
use Basho\Riak\Core\Query\RiakObjectList;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;

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
     * @var \Basho\Riak\Converter\RiakObjectConverter
     */
    private $objectConverter;

    /**
     * @var \Basho\Riak\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Converter\ConverterFactory    $converterFactory
     * @param \Basho\Riak\Converter\RiakObjectConverter $objectConverter
     * @param \Basho\Riak\Core\Query\RiakLocation       $location
     * @param array                                     $options
     */
    public function __construct(ConverterFactory $converterFactory, RiakObjectConverter $objectConverter, RiakLocation $location, $options)
    {
        $this->converterFactory = $converterFactory;
        $this->objectConverter  = $objectConverter;
        $this->location         = $location;
        $this->options          = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $objectList  = new RiakObjectList([]);
        $getRequest  = $this->createGetRequest();
        $getResponse = $adapter->send($getRequest);
        $notFound    = $getResponse === null;
        $unchanged   = false;

        if ( ! $notFound) {
            $vClock      = $getResponse->vClock;
            $unchanged   = $getResponse->unchanged;
            $contentList = $getResponse->contentList;
            $objectList      = $this->objectConverter->convertToRiakObjectList($contentList, $vClock);
        }

        $response = new FetchValueResponse($this->converterFactory, $this->location, $objectList);

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

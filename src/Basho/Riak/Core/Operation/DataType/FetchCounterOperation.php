<?php

namespace Basho\Riak\Core\Operation\DataType;

use Basho\Riak\Command\DataType\Response\FetchCounterResponse;
use Basho\Riak\Converter\CrdtResponseConverter;
use Basho\Riak\Core\Message\DataType\GetRequest;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;

/**
 * An operation used to fetch a counter from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchCounterOperation implements RiakOperation
{
    /**
     * @var \Basho\Riak\Converter\CrdtResponseConverter
     */
    private $converter;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Converter\CrdtResponseConverter $converter
     * @param \Basho\Riak\Core\Query\RiakLocation         $location
     * @param array                                       $options
     */
    public function __construct(CrdtResponseConverter $converter, RiakLocation $location, array $options)
    {
        $this->converter = $converter;
        $this->location  = $location;
        $this->options   = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $getRequest  = $this->createGetRequest();
        $getResponse = $adapter->send($getRequest);
        $counter     = $this->converter->convertCounter($getResponse);
        $response    = new FetchCounterResponse($this->location, $counter);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\DataType\GetRequest
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
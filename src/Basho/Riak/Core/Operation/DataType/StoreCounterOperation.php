<?php

namespace Basho\Riak\Core\Operation\DataType;

use Basho\Riak\Command\DataType\Response\StoreCounterResponse;
use Basho\Riak\Core\Converter\CrdtResponseConverter;
use Basho\Riak\Core\Message\DataType\PutRequest;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;

/**
 * An operation used to store a counter from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreCounterOperation implements RiakOperation
{
    /**
     * @var \Basho\Riak\Core\Converter\CrdtResponseConverter
     */
    private $converter;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Core\Query\Crdt\RiakCounter
     */
    private $counter;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Core\Converter\CrdtResponseConverter $converter
     * @param \Basho\Riak\Core\Query\RiakLocation              $location
     * @param \Basho\Riak\Core\Query\Crdt\RiakCounter          $counter
     * @param array                                            $options
     */
    public function __construct(CrdtResponseConverter $converter, RiakLocation $location, RiakCounter $counter, array $options)
    {
        $this->converter = $converter;
        $this->location  = $location;
        $this->options   = $options;
        $this->counter   = $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $putRequest  = $this->createGetRequest();
        $putResponse = $adapter->send($putRequest);
        $counter     = $this->converter->convertCounter($putResponse);
        $response    = new StoreCounterResponse($this->location, $counter);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\DataType\PutRequest
     */
    private function createGetRequest()
    {
        $request   = new PutRequest();
        $namespace = $this->location->getNamespace();

        $request->type   = $namespace->getBucketType();
        $request->bucket = $namespace->getBucketName();
        $request->key    = $this->location->getKey();
        $request->op     = new CounterOp($this->counter->getValue());

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }
}

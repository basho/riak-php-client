<?php

namespace Basho\Riak\Core\Operation;

use Basho\Riak\Command\Kv\Response\DeleteValueResponse;
use Basho\Riak\Core\Converter\RiakObjectConverter;
use Basho\Riak\Core\Converter\ConverterFactory;
use Basho\Riak\Core\Message\DeleteRequest;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;
use Basho\Riak\Cap\VClock;

/**
 * An operation used to delete an object from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteOperation implements RiakOperation
{
    /**
     * @var \Basho\Riak\Core\Converter\RiakObjectConverter
     */
    private $objectConverter;

    /**
     * @var \Basho\Riak\Core\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Cap\VClock
     */
    private $vClock;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Core\Converter\ConverterFactory    $converterFactory
     * @param \Basho\Riak\Core\Converter\RiakObjectConverter $objectConverter
     * @param \Basho\Riak\Command\Kv\RiakLocation            $location
     * @param array                                          $options
     * @param \Basho\Riak\Cap\VClock                         $vClock
     */
    public function __construct(ConverterFactory $converterFactory, RiakObjectConverter $objectConverter, RiakLocation $location, array $options, VClock $vClock = null)
    {
        $this->converterFactory = $converterFactory;
        $this->objectConverter  = $objectConverter;
        $this->location         = $location;
        $this->location         = $location;
        $this->options          = $options;
        $this->vClock           = $vClock;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $putRequest  = $this->createDeleteRequest();
        $putResponse = $adapter->send($putRequest);

        $vClock      = $putResponse->vClock;
        $contentList = $putResponse->contentList;
        $values      = $this->objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new DeleteValueResponse($this->converterFactory, $this->location, $values);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\DeleteRequest
     */
    private function createDeleteRequest()
    {
        $request   = new DeleteRequest();
        $namespace = $this->location->getNamespace();

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        $request->key     = $this->location->getKey();
        $request->type    = $namespace->getBucketType();
        $request->bucket  = $namespace->getBucketName();
        $request->vClock  = $this->vClock ? $this->vClock->getValue() : null;

        return $request;
    }
}
<?php

namespace Basho\Riak\Core\Operation;

use Basho\Riak\Command\Kv\Response\StoreValueResponse;
use Basho\Riak\Core\Converter\DomainObjectReference;
use Basho\Riak\Core\Converter\RiakObjectConverter;
use Basho\Riak\Core\Converter\ConverterFactory;
use Basho\Riak\Core\Message\PutRequest;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakObject;
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
class StoreOperation implements RiakOperation
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
     * @var \Basho\Riak\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Core\Converter\ConverterFactory    $converterFactory
     * @param \Basho\Riak\Core\Converter\RiakObjectConverter $objectConverter
     * @param \Basho\Riak\Command\Kv\RiakLocation            $location
     * @param \Basho\Riak\Core\Query\RiakObject|mixed        $value
     * @param array                                          $options
     */
    public function __construct(ConverterFactory $converterFactory, RiakObjectConverter $objectConverter, RiakLocation $location, $value, array $options)
    {
        $this->converterFactory = $converterFactory;
        $this->objectConverter  = $objectConverter;
        $this->location         = $location;
        $this->location         = $location;
        $this->options          = $options;
        $this->value            = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $putRequest  = $this->createPutRequest();
        $putResponse = $adapter->send($putRequest);

        $vClock      = $putResponse->vClock;
        $contentList = $putResponse->contentList;
        $values      = $this->objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new StoreValueResponse($this->converterFactory, $this->location, $values);

        $response->setGeneratedKey($putResponse->key);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\PutRequest
     */
    private function createPutRequest()
    {
        $request     = new PutRequest();
        $riakObject  = $this->getConvertedValue();
        $namespace   = $this->location->getNamespace();
        $vClockValue = $riakObject->getVClock() ? $riakObject->getVClock()->getValue() : null;

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        $request->vClock  = $vClockValue;
        $request->key     = $this->location->getKey();
        $request->type    = $namespace->getBucketType();
        $request->bucket  = $namespace->getBucketName();
        $request->content = $this->objectConverter->convertToRiakContent($riakObject);

        return $request;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    private function getConvertedValue()
    {
        if ($this->value instanceof RiakObject) {
            return $this->value;
        }

        if ($this->value === null) {
            return new RiakObject();
        }

        $type      = $this->getValueType();
        $converter = $this->converterFactory->getConverter($type);
        $reference = new DomainObjectReference($this->value, $this->location);

        return $converter->fromDomain($reference);
    }

    /**
     * @return string
     */
    private function getValueType()
    {
        return is_object($this->value)
            ? get_class($this->value)
            : gettype($this->value);
    }
}

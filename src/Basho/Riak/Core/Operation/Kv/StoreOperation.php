<?php

namespace Basho\Riak\Core\Operation\Kv;

use Basho\Riak\Command\Kv\Response\StoreValueResponse;
use Basho\Riak\Converter\DomainObjectReference;
use Basho\Riak\Core\Message\Kv\PutRequest;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakObject;
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
class StoreOperation implements RiakOperation
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
     * @var \Basho\Riak\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\RiakConfig                    $config
     * @param \Basho\Riak\Core\Query\RiakLocation       $location
     * @param \Basho\Riak\Core\Query\RiakObject|mixed   $value
     * @param array                                     $options
     */
    public function __construct(RiakConfig $config, RiakLocation $location, $value, array $options)
    {
        $this->location = $location;
        $this->options  = $options;
        $this->config   = $config;
        $this->value    = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $putRequest       = $this->createPutRequest();
        $putResponse      = $adapter->send($putRequest);
        $resolverFactory  = $this->config->getResolverFactory();
        $converterFactory = $this->config->getConverterFactory();
        $objectConverter  = $this->config->getRiakObjectConverter();

        $vClock      = $putResponse->vClock;
        $contentList = $putResponse->contentList;
        $values      = $objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new StoreValueResponse($converterFactory, $resolverFactory, $this->location, $values);

        $response->setGeneratedKey($putResponse->key);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\Kv\PutRequest
     */
    private function createPutRequest()
    {
        $request          = new PutRequest();
        $riakObject       = $this->getConvertedValue();
        $namespace        = $this->location->getNamespace();
        $objectConverter  = $this->config->getRiakObjectConverter();
        $vClockValue      = $riakObject->getVClock() ? $riakObject->getVClock()->getValue() : null;

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        $request->vClock  = $vClockValue;
        $request->key     = $this->location->getKey();
        $request->type    = $namespace->getBucketType();
        $request->bucket  = $namespace->getBucketName();
        $request->content = $objectConverter->convertToRiakContent($riakObject);

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
        $factory   = $this->config->getConverterFactory();
        $converter = $factory->getConverter($type);
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

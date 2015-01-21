<?php

namespace Basho\Riak\Command\Kv\Response;

use Basho\Riak\RiakResponse;
use Basho\Riak\Core\Query\RiakList;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Resolver\ResolverFactory;
use Basho\Riak\Converter\ConverterFactory;
use Basho\Riak\Core\Query\DomainObjectList;
use Basho\Riak\Converter\RiakObjectReference;

/**
 * Base Response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Basho\Riak\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Basho\Riak\Resolver\ResolverFactory
     */
    private $resolverFactory;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Core\Query\RiakList
     */
    private $values;

    /**
     * @param \Basho\Riak\Converter\ConverterFactory $converterFactory
     * @param \Basho\Riak\Resolver\ResolverFactory   $resolverFactory
     * @param \Basho\Riak\Core\Query\RiakLocation    $location
     * @param \Basho\Riak\Core\Query\RiakList        $values
     */
    public function __construct(ConverterFactory $converterFactory, ResolverFactory $resolverFactory, RiakLocation $location, RiakList $values)
    {
        $this->converterFactory = $converterFactory;
        $this->resolverFactory  = $resolverFactory;
        $this->location         = $location;
        $this->values           = $values;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Determine if this response contains any returned values.
     *
     * @return boolean.
     */
    public function hasValues()
    {
        return ( ! $this->values->isEmpty());
    }

    /**
     * Return the number of values contained in this response.
     *
     * @return integer
     */
    public function getNumberOfValues()
    {
        return $this->values->count();
    }

    /**
     * Get all the objects returned in this response.
     * If a type is provided will be converted to the supplied class using the convert api.
     *
     * @param string $type
     *
     * @return \Basho\Riak\Core\Query\RiakList
     */
    public function getValues($type = null)
    {
        if ($type == null) {
            return $this->values;
        }

        $converter  = $this->converterFactory->getConverter($type);
        $resultList = [];

        foreach ($this->values as $riakObject) {
            $reference = new RiakObjectReference($riakObject, $this->location, $type);
            $converted = $converter->toDomain($reference);

            $resultList[] = $converted;
        }

        return new DomainObjectList($resultList);
    }

    /**
     * Get a single, resolved object from this response.
     *
     * @param string $type
     *
     * @return \Basho\Riak\Core\Query\RiakObject
     *
     * @throws \Basho\Riak\Resolver\UnresolvedConflictException
     */
    public function getValue($type = null)
    {
        $siblings   = $this->getValues($type);
        $resolver   = $this->resolverFactory->getResolver($type);
        $riakObject = $resolver->resolve($siblings);

        return $riakObject;
    }

    /**
     * Get the vector clock returned with this response.
     *
     * @return \Basho\Riak\Cap\VClock
     */
    public function getVectorClock()
    {
        if ($this->values->isEmpty()) {
            return;
        }

        $first  = $this->values->first();
        $vclock = $first->getVClock();

        return $vclock;
    }
}

<?php

namespace Basho\Riak\Command\Kv\Response;

use Basho\Riak\RiakResponse;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Converter\ConverterFactory;
use Basho\Riak\Core\Converter\RiakObjectReference;

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
     * @var \Basho\Riak\Core\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Core\Query\RiakObject[]
     */
    private $values;

    /**
     * @param \Basho\Riak\Core\Converter\ConverterFactory $converterFactory
     * @param \Basho\Riak\Core\Query\RiakLocation         $location
     * @param array                                       $values
     */
    public function __construct(ConverterFactory $converterFactory, RiakLocation $location, array $values)
    {
        $this->converterFactory = $converterFactory;
        $this->location         = $location;
        $this->values           = $values;
    }

    /**
     * Determine if this response contains any returned values.
     *
     * @return boolean.
     */
    public function hasValues()
    {
        return ! empty($this->values);
    }

    /**
     * Return the number of values contained in this response.
     *
     * @return integer
     */
    public function getNumberOfValues()
    {
        return count($this->values);
    }

    /**
     * Get all the objects returned in this response.
     *
     * @return \Basho\Riak\Core\Query\RiakObject[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Get all the objects returned in this response.
     * The values will be converted to the supplied class using the convert api.
     *
     * @param string $type
     *
     * @return array
     */
    public function getValuesAs($type)
    {
        $converter  = $this->converterFactory->getConverter($type);
        $resultList = [];

        foreach ($this->values as $riakObject) {
            $reference = new RiakObjectReference($riakObject, $this->location, $type);
            $converted = $converter->toDomain($reference);

            $resultList[] = $converted;
        }

        return $resultList;
    }

    /**
     * Get the vector clock returned with this response.
     *
     * @return \Basho\Riak\Cap\VClock
     */
    public function getVectorClock()
    {
        if ( ! $this->hasValues()) {
            return null;
        }

        $first  = reset($this->values);
        $vclock = $first->getVClock();

        return $vclock;
    }
}

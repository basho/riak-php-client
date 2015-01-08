<?php

namespace Basho\Riak\Core\Converter;

use Basho\Riak\Core\Converter\Hydrator\DomainHydrator;

/**
 * Holds instances of converters to be used for serialization / deserialization  of objects stored and fetched from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class ConverterFactory
{
    /**
     * @var \Basho\Riak\Core\Converter\Converter[]
     */
    private $converters;

    /**
     * @var \Basho\Riak\Core\Converter\Hydrator\DomainHydrator
     */
    private $domainHydrator;

    /**
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainHydrator $domainHydrator
     */
    public function __construct(DomainHydrator $domainHydrator)
    {
        $this->domainHydrator = $domainHydrator;
    }

    /**
     * @return \Basho\Riak\Core\Converter\Converter[]
     */
    public function getConverters()
    {
        return $this->converters;
    }

    /**
     * @param string $type
     *
     * @return \Basho\Riak\Core\Converter\Converter
     */
    public function getConverter($type)
    {
        if (isset($this->converters[$type])) {
            return $this->converters[$type];
        }

        return $this->converters[$type] = new JsonConverter($this->domainHydrator);
    }

    /**
     * @param type                                  $type
     * @param \Basho\Riak\Core\Converter\Converter  $converter
     */
    public function addConverter($type, Converter $converter)
    {
        $this->converters[$type] = $converter;
    }
}

<?php

namespace Basho\Riak;

use Basho\Riak\Core\Converter\ConverterFactory;
use Basho\Riak\Core\Converter\RiakObjectConverter;
use Basho\Riak\Core\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader;

/**
 * Riak client config.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakConfig
{
    /**
     * @var \Basho\Riak\Core\Converter\RiakObjectConverter
     */
    private $riakObjectConverter;

    /**
     * @var \Basho\Riak\Core\Converter\Hydrator\DomainHydrator
     */
    private $domainHydrator;

    /**
     * @var \Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader
     */
    private $domainMetadataReader;

    /**
     * @var \Basho\Riak\Core\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @param \Basho\Riak\Core\Converter\ConverterFactory               $converterFactory
     * @param \Basho\Riak\Core\Converter\RiakObjectConverter            $riakObjectConverter
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader  $domainMetadataReader
     * @param \Basho\Riak\Core\Converter\ConverterFactory               $domainHydrator
     */
    public function __construct(
        ConverterFactory     $converterFactory,
        RiakObjectConverter  $riakObjectConverter,
        DomainMetadataReader $domainMetadataReader,
        DomainHydrator       $domainHydrator
    ) {
        $this->converterFactory     = $converterFactory;
        $this->riakObjectConverter  = $riakObjectConverter;
        $this->domainMetadataReader = $domainMetadataReader;
        $this->domainHydrator       = $domainHydrator;
    }

    /**
     * @return \Basho\Riak\Core\Converter\RiakObjectConverter
     */
    public function getRiakObjectConverter()
    {
        return $this->riakObjectConverter;
    }

    /**
     * @return \Basho\Riak\Core\Converter\Hydrator\DomainHydrator
     */
    public function getDomainHydrator()
    {
        return $this->domainHydrator;
    }

    /**
     * @return \Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader
     */
    public function getDomainMetadataReader()
    {
        return $this->domainMetadataReader;
    }

    /**
     * @return \Basho\Riak\Core\Converter\ConverterFactory
     */
    public function getConverterFactory()
    {
        return $this->converterFactory;
    }
}

<?php

namespace Basho\Riak;

use Basho\Riak\Converter\ConverterFactory;
use Basho\Riak\Converter\RiakObjectConverter;
use Basho\Riak\Converter\CrdtResponseConverter;
use Basho\Riak\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Converter\Hydrator\DomainMetadataReader;

/**
 * Riak client config.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakConfig
{
    /**
     * @var \Basho\Riak\Converter\RiakObjectConverter
     */
    private $riakObjectConverter;

    /**
     * @var \Basho\Riak\Converter\CrdtResponseConverter
     */
    private $crdtResponseConverter;

    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    private $domainHydrator;

    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainMetadataReader
     */
    private $domainMetadataReader;

    /**
     * @var \Basho\Riak\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @param \Basho\Riak\Converter\ConverterFactory               $converterFactory
     * @param \Basho\Riak\Converter\RiakObjectConverter            $riakObjectConverter
     * @param \Basho\Riak\Converter\CrdtResponseConverter          $crdtResponseConverter
     * @param \Basho\Riak\Converter\Hydrator\DomainMetadataReader  $domainMetadataReader
     * @param \Basho\Riak\Converter\ConverterFactory               $domainHydrator
     */
    public function __construct(
        ConverterFactory      $converterFactory,
        RiakObjectConverter   $riakObjectConverter,
        CrdtResponseConverter $crdtResponseConverter,
        DomainMetadataReader  $domainMetadataReader,
        DomainHydrator        $domainHydrator
    ) {
        $this->converterFactory      = $converterFactory;
        $this->riakObjectConverter   = $riakObjectConverter;
        $this->crdtResponseConverter = $crdtResponseConverter;
        $this->domainMetadataReader  = $domainMetadataReader;
        $this->domainHydrator        = $domainHydrator;
    }

    /**
     * @return \Basho\Riak\Converter\RiakObjectConverter
     */
    public function getRiakObjectConverter()
    {
        return $this->riakObjectConverter;
    }

    /**
     * @return \Basho\Riak\Converter\CrdtResponseConverter
     */
    public function getCrdtResponseConverter()
    {
        return $this->crdtResponseConverter;
    }

    /**
     * @return \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    public function getDomainHydrator()
    {
        return $this->domainHydrator;
    }

    /**
     * @return \Basho\Riak\Converter\Hydrator\DomainMetadataReader
     */
    public function getDomainMetadataReader()
    {
        return $this->domainMetadataReader;
    }

    /**
     * @return \Basho\Riak\Converter\ConverterFactory
     */
    public function getConverterFactory()
    {
        return $this->converterFactory;
    }
}

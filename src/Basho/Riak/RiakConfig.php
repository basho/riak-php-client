<?php

namespace Basho\Riak;

use Basho\Riak\Core\Converter\ConverterFactory;
use Basho\Riak\Core\Converter\RiakObjectConverter;
use Basho\Riak\Core\Converter\CrdtResponseConverter;
use Basho\Riak\Core\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader;

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
     * @var \Basho\Riak\Core\Converter\RiakObjectConverter
     */
    private $riakObjectConverter;

    /**
     * @var \Basho\Riak\Core\Converter\CrdtResponseConverter
     */
    private $crdtResponseConverter;

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
     * @param \Basho\Riak\Core\Converter\CrdtResponseConverter          $crdtResponseConverter
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader  $domainMetadataReader
     * @param \Basho\Riak\Core\Converter\ConverterFactory               $domainHydrator
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
     * @return \Basho\Riak\Core\Converter\RiakObjectConverter
     */
    public function getRiakObjectConverter()
    {
        return $this->riakObjectConverter;
    }

    /**
     * @return \Basho\Riak\Core\Converter\CrdtResponseConverter
     */
    public function getCrdtResponseConverter()
    {
        return $this->crdtResponseConverter;
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

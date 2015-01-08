<?php

namespace Basho\Riak;

use Doctrine\Common\Annotations\AnnotationReader;
use Basho\Riak\RiakConfig;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\RiakNodeBuilder;
use Basho\Riak\Core\Converter\ConverterFactory;
use Basho\Riak\Core\Converter\RiakObjectConverter;
use Basho\Riak\Core\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader;

/**
 * Build a riak client
 *
 * @todo split into smaller builders
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakClientBuilder
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
     * @var \Basho\Riak\Core\RiakCluster
     */
    private $cluster;

    /**
     * @var \Basho\Riak\RiakConfig
     */
    private $config;

     /**
     * @var array
     */
    private $nodes = [];

    /**
     * @return \Basho\Riak\Core\Converter\RiakObjectConverter
     */
    private function getRiakObjectConverter()
    {
        if ($this->riakObjectConverter === null) {
            $this->riakObjectConverter = new RiakObjectConverter();
        }

        return $this->riakObjectConverter;
    }

    /**
     * @param \Basho\Riak\Core\Converter\RiakObjectConverter $converter
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withRiakObjectConverter(RiakObjectConverter $converter)
    {
        $this->riakObjectConverter = $converter;

        return $this;
    }

    /**
     * @return \Basho\Riak\Core\Converter\Hydrator\DomainHydrator
     */
    private function getDomainHydrator()
    {
        if ($this->domainHydrator === null) {
            $this->domainHydrator = new DomainHydrator($this->getDomainMetadataReader());
        }

        return $this->domainHydrator;
    }

    /**
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainHydrator $hydrator
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withDomainHydrator(DomainHydrator $hydrator)
    {
        $this->domainHydrator = $hydrator;

        return $this;
    }

    /**
     * @return \Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader
     */
    private function getDomainMetadataReader()
    {
        if ($this->domainMetadataReader === null) {
            $this->domainMetadataReader = new DomainMetadataReader(new AnnotationReader());
        }

        return $this->domainMetadataReader;
    }

    /**
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainMetadataReader $reader
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withDomainMetadataReader(DomainMetadataReader $reader)
    {
        $this->domainMetadataReader = $reader;

        return $this;
    }

    /**
     * @return \Basho\Riak\Core\Converter\ConverterFactory
     */
    private function getConverterFactory()
    {
        if ($this->converterFactory === null) {
            $this->converterFactory = new ConverterFactory($this->getDomainHydrator());
        }

        return $this->converterFactory;
    }

    /**
     * @param \Basho\Riak\Core\Converter\ConverterFactory $converterFactory
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withConverterFactory(ConverterFactory $converterFactory)
    {
        $this->converterFactory = $converterFactory;

        return $this;
    }

    /**
     * @param \Basho\Riak\Core\RiakCluster $cluster
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withCluster(RiakCluster $cluster)
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @return \Basho\Riak\Core\RiakCluster
     */
    private function getCluster()
    {
        if ($this->cluster === null) {
            $this->cluster = new RiakCluster($this->getConfig());
        }

        return $this->cluster;
    }

    /**
     * @param \Basho\Riak\RiakConfig $config
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withConfig(RiakConfig $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return \Basho\Riak\RiakConfig
     */
    private function getConfig()
    {
        if ($this->config === null) {
            $this->config = new RiakConfig(
                $this->getConverterFactory(),
                $this->getRiakObjectConverter(),
                $this->getDomainMetadataReader(),
                $this->getDomainHydrator()
            );
        }

        return $this->config;
    }

    /**
     * Adds a RiakNode to this cluster.
     *
     * @param \Basho\Riak\Core\RiakNode[] $node
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withNode(RiakNode $node)
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * Creates a RiakNode base on the given URI and add it to this cluster.
     *
     * @param string $uri
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withNodeUri($uri)
    {
        $builder = new RiakNodeBuilder();
        $node    = $builder
            ->withProtocol(parse_url($uri, PHP_URL_SCHEME))
            ->withHost(parse_url($uri, PHP_URL_HOST))
            ->withPort(parse_url($uri, PHP_URL_PORT))
            ->withUser(parse_url($uri, PHP_URL_USER))
            ->withPass(parse_url($uri, PHP_URL_PASS))
            ->build();

        return $this->withNode($node);
    }

    /**
     * Create a riak client
     *
     * @return \Basho\Riak\RiakClient
     */
    public function build()
    {
        $config  = $this->getConfig();
        $cluster = $this->getCluster();

        $cluster->setNodes($this->nodes);

        return new RiakClient($config, $cluster);
    }
}
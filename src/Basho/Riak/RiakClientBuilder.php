<?php

namespace Basho\Riak;

use Doctrine\Common\Annotations\AnnotationReader;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Converter\Converter;
use Basho\Riak\Core\RiakNodeBuilder;
use Basho\Riak\Resolver\ResolverFactory;
use Basho\Riak\Resolver\ConflictResolver;
use Basho\Riak\Converter\ConverterFactory;
use Basho\Riak\Converter\RiakObjectConverter;
use Basho\Riak\Converter\CrdtResponseConverter;
use Basho\Riak\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Converter\Hydrator\DomainMetadataReader;

/**
 * Build a riak client
 *
 * @todo split into smaller builders
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakClientBuilder
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
     * @var \Basho\Riak\Resolver\ResolverFactory
     */
    private $resolverFactory;

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
     * @var \Basho\Riak\Resolver\ConflictResolver[]
     */
    private $resolvers = [];

    /**
     * @var \Basho\Riak\Converter\Converter[]
     */
    private $converters = [];

    /**
     * @return \Basho\Riak\Converter\RiakObjectConverter
     */
    private function getRiakObjectConverter()
    {
        if ($this->riakObjectConverter === null) {
            $this->riakObjectConverter = new RiakObjectConverter();
        }

        return $this->riakObjectConverter;
    }

    /**
     * @param \Basho\Riak\Converter\RiakObjectConverter $converter
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withRiakObjectConverter(RiakObjectConverter $converter)
    {
        $this->riakObjectConverter = $converter;

        return $this;
    }

    /**
     * @return \Basho\Riak\Converter\CrdtResponseConverter
     */
    public function getCrdtResponseConverter()
    {
        if ($this->crdtResponseConverter === null) {
            $this->crdtResponseConverter = new CrdtResponseConverter();
        }

        return $this->crdtResponseConverter;
    }

    /**
     * @param \Basho\Riak\Converter\CrdtResponseConverter $converter
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withCrdtResponseConverter(CrdtResponseConverter $converter)
    {
        $this->crdtResponseConverter = $converter;

        return $this;
    }

    /**
     * @return \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    private function getDomainHydrator()
    {
        if ($this->domainHydrator === null) {
            $this->domainHydrator = new DomainHydrator($this->getDomainMetadataReader());
        }

        return $this->domainHydrator;
    }

    /**
     * @param \Basho\Riak\Converter\Hydrator\DomainHydrator $hydrator
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withDomainHydrator(DomainHydrator $hydrator)
    {
        $this->domainHydrator = $hydrator;

        return $this;
    }

    /**
     * @return \Basho\Riak\Converter\Hydrator\DomainMetadataReader
     */
    private function getDomainMetadataReader()
    {
        if ($this->domainMetadataReader === null) {
            $this->domainMetadataReader = new DomainMetadataReader(new AnnotationReader());
        }

        return $this->domainMetadataReader;
    }

    /**
     * @param \Basho\Riak\Converter\Hydrator\DomainMetadataReader $reader
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withDomainMetadataReader(DomainMetadataReader $reader)
    {
        $this->domainMetadataReader = $reader;

        return $this;
    }

    /**
     * @return \Basho\Riak\Converter\ConverterFactory
     */
    private function getConverterFactory()
    {
        if ($this->converterFactory === null) {
            $this->converterFactory = new ConverterFactory($this->getDomainHydrator());
        }

        return $this->converterFactory;
    }

    /**
     * @param \Basho\Riak\Converter\ConverterFactory $factory
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withConverterFactory(ConverterFactory $factory)
    {
        $this->converterFactory = $factory;

        return $this;
    }

    /**
     * @return \Basho\Riak\Resolver\ResolverFactory
     */
    private function getResolverFactoryy()
    {
        if ($this->resolverFactory === null) {
            $this->resolverFactory = new ResolverFactory();
        }

        return $this->resolverFactory;
    }

    /**
     * @param \Basho\Riak\Resolver\ResolverFactory $factory
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withResolverFactoryy(ConverterFactory $factory)
    {
        $this->resolverFactory = $factory;

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
                $this->getResolverFactoryy(),
                $this->getRiakObjectConverter(),
                $this->getCrdtResponseConverter(),
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
     * @param string                                $type
     * @param \Basho\Riak\Resolver\ConflictResolver $resolver
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withConflictResolver($type, ConflictResolver $resolver)
    {
        $this->resolvers[$type] = $resolver;

        return $this;
    }

    /**
     * @param string                          $type
     * @param \Basho\Riak\Converter\Converter $converter
     *
     * @return \Basho\Riak\RiakClientBuilder
     */
    public function withConverter($type, Converter $converter)
    {
        $this->converters[$type] = $converter;

        return $this;
    }

    /**
     * Create a riak client
     *
     * @return \Basho\Riak\RiakClient
     */
    public function build()
    {
        $config           = $this->getConfig();
        $cluster          = $this->getCluster();
        $resolverFactory  = $config->getResolverFactory();
        $converterFactory = $config->getConverterFactory();

        $cluster->setNodes($this->nodes);
        $resolverFactory->setResolvers($this->resolvers);
        $converterFactory->setConverters($this->converters);

        return new RiakClient($config, $cluster);
    }
}

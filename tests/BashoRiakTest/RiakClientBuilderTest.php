<?php

namespace BashoRiakTest;

use Basho\Riak\RiakClientBuilder;

class RiakClientBuilderTest extends TestCase
{
    /**
     * @var \Basho\Riak\RiakClientBuilder
     */
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new RiakClientBuilder();
    }

    public function testBuildWithHttpNode()
    {
        $client = $this->builder
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Basho\Riak\RiakClient', $client);

        $cluster = $client->getCluster();
        $config  = $client->getConfig();
        $nodes   = $cluster->getNodes();

        $this->assertCount(1, $nodes);
        $this->assertInstanceOf('Basho\Riak\RiakConfig', $config);
        $this->assertSame($config, $cluster->getRiakConfig());
        $this->assertInstanceOf('Basho\Riak\Core\RiakNode', $nodes[0]);
        $this->assertInstanceOf('Basho\Riak\Core\RiakCluster', $cluster);
        $this->assertInstanceOf('Basho\Riak\Core\RiakHttpAdpter', $nodes[0]->getAdapter());
        $this->assertInstanceOf('Basho\Riak\Converter\ConverterFactory', $config->getConverterFactory());
        $this->assertInstanceOf('Basho\Riak\Converter\Hydrator\DomainHydrator', $config->getDomainHydrator());
        $this->assertInstanceOf('Basho\Riak\Converter\RiakObjectConverter', $config->getRiakObjectConverter());
        $this->assertInstanceOf('Basho\Riak\Converter\Hydrator\DomainMetadataReader', $config->getDomainMetadataReader());
    }

    public function testBuildWithNode()
    {
        $node                 = $this->getMock('Basho\Riak\Core\RiakNode', [], [], '', false);
        $cluster              = $this->getMock('Basho\Riak\Core\RiakCluster', [], [], '', false);
        $converterFactory     = $this->getMock('Basho\Riak\Converter\ConverterFactory', [], [], '', false);
        $objectConverter      = $this->getMock('Basho\Riak\Converter\RiakObjectConverter', [], [], '', false);
        $domainHydrator       = $this->getMock('Basho\Riak\Converter\Hydrator\DomainHydrator', [], [], '', false);
        $domainMetadataReader = $this->getMock('Basho\Riak\Converter\Hydrator\DomainMetadataReader', [], [], '', false);

        $cluster->expects($this->once())
            ->method('setNodes')
            ->with($this->equalTo([$node]));

        $client = $this->builder
            ->withDomainMetadataReader($domainMetadataReader)
            ->withRiakObjectConverter($objectConverter)
            ->withConverterFactory($converterFactory)
            ->withDomainHydrator($domainHydrator)
            ->withCluster($cluster)
            ->withNode($node)
            ->build();

        $this->assertInstanceOf('Basho\Riak\RiakClient', $client);

        $riakCluster = $client->getCluster();
        $riakConfig  = $client->getConfig();

        $this->assertSame($cluster, $riakCluster);
        $this->assertSame($riakConfig, $riakConfig);
        $this->assertSame($domainHydrator, $riakConfig->getDomainHydrator());
        $this->assertSame($converterFactory, $riakConfig->getConverterFactory());
        $this->assertSame($objectConverter, $riakConfig->getRiakObjectConverter());
        $this->assertSame($domainMetadataReader, $riakConfig->getDomainMetadataReader());
    }

    public function testBuildWithConflictResolver()
    {
        $resolver = $this->getMock('Basho\Riak\Resolver\ConflictResolver');
        $client   = $this->builder
            ->withConflictResolver('stdClass', $resolver)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Basho\Riak\RiakClient', $client);

        $config  = $client->getConfig();
        $factory = $config->getResolverFactory();

        $this->assertInstanceOf('Basho\Riak\Resolver\ResolverFactory', $factory);
        $this->assertSame($resolver, $factory->getResolver('stdClass'));
    }

    public function testBuildWithConverter()
    {
        $converter = $this->getMock('Basho\Riak\Converter\Converter');
        $client    = $this->builder
            ->withConverter('stdClass', $converter)
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->assertInstanceOf('Basho\Riak\RiakClient', $client);

        $config  = $client->getConfig();
        $factory = $config->getConverterFactory();

        $this->assertInstanceOf('Basho\Riak\Converter\ConverterFactory', $factory);
        $this->assertSame($converter, $factory->getConverter('stdClass'));
    }
}

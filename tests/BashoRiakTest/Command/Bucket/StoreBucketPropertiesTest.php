<?php

namespace BashoRiakTest\Command\Bucket;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

class StoreBucketPropertiesTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var \Basho\Riak\RiakClient
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\RiakAdapter
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->adapter   = $this->getMock('Basho\Riak\Core\RiakAdapter');
        $this->namespace = new RiakNamespace('bucket', 'type');
        $this->node      = new RiakNode($this->adapter);
        $this->client    = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $builder = StoreBucketProperties::builder()
            ->withNamespace($this->namespace);

        $this->assertInstanceOf('Basho\Riak\Command\Bucket\StoreBucketProperties', $builder->build());
    }

    /**
     * @expectedException \Basho\Riak\RiakException
     * @expectedExceptionMessage Not implemented
     */
    public function testExecuteCommand()
    {
        $this->client->execute(StoreBucketProperties::builder()
            ->withNamespace($this->namespace)
            ->build());
    }
}
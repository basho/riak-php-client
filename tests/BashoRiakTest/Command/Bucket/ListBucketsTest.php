<?php

namespace BashoRiakTest\Command\Bucket;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Command\Bucket\ListBuckets;

class ListBucketsTest extends TestCase
{
    /**
     * @var string
     */
    private $type;

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
        $this->node      = new RiakNode($this->adapter);
        $this->type      = 'type';
        $this->client    = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $builder = ListBuckets::builder()
            ->withType($this->type);

        $this->assertInstanceOf('Basho\Riak\Command\Bucket\ListBuckets', $builder->build());
    }

    /**
     * @expectedException \Basho\Riak\RiakException
     * @expectedExceptionMessage Not implemented
     */
    public function testExecuteCommand()
    {
        $this->client->execute(ListBuckets::builder()
            ->withType($this->type)
            ->build());
    }
}
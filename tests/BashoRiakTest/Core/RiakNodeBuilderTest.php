<?php

namespace BashoRiakTest\Core;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNodeBuilder;

class RiakNodeBuilderTest extends TestCase
{
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new RiakNodeBuilder();
    }

    public function testBuildHttpNode()
    {
        $node = $this->builder
            ->withProtocol('http')
            ->withHost('localhost')
            ->withPort('8098')
            ->build();

        $this->assertInstanceOf('Basho\Riak\Core\RiakNode', $node);
        $this->assertInstanceOf('Basho\Riak\Core\RiakHttpAdpter', $node->getAdapter());
        $this->assertInstanceOf('GuzzleHttp\Client', $node->getAdapter()->getClient());

        $adpter  = $node->getAdapter();
        $client  = $adpter->getClient();
        $baseUrl = $client->getBaseUrl();
        $auth    = $client->getDefaultOption('auth');

        $this->assertEquals('http://localhost:8098', $baseUrl);
        $this->assertNull($auth);
    }

    public function testBuildHttpNodeWithAuth()
    {
        $node = $this->builder
            ->withProtocol('https')
            ->withHost('localhost')
            ->withPort('8098')
            ->withUser('http_user')
            ->withPass('http_pass')
            ->build();

        $this->assertInstanceOf('Basho\Riak\Core\RiakNode', $node);
        $this->assertInstanceOf('Basho\Riak\Core\RiakHttpAdpter', $node->getAdapter());
        $this->assertInstanceOf('GuzzleHttp\Client', $node->getAdapter()->getClient());

        $adpter  = $node->getAdapter();
        $client  = $adpter->getClient();
        $baseUrl = $client->getBaseUrl();
        $auth    = $client->getDefaultOption('auth');

        $this->assertEquals('https://localhost:8098', $baseUrl);
        $this->assertEquals(['http_user', 'http_pass'], $auth);
    }

    /**
     * @expectedException \Basho\Riak\RiakException
     * @expectedExceptionMessage Unknown protocol : NOT_VALID
     */
    public function testBuildNodeInvalidProtocolException()
    {
        $this->builder
            ->withProtocol('NOT_VALID')
            ->withHost('localhost')
            ->withPort('8098')
            ->build();
    }
}
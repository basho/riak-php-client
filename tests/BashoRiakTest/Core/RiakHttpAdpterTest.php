<?php

namespace BashoRiakTest\Core;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakHttpAdpter;
use Basho\Riak\Core\Message\Kv\GetRequest;
use Basho\Riak\Core\Message\Kv\PutRequest;
use Basho\Riak\Core\Message\Kv\DeleteRequest;

class RiakHttpAdpterTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\RiakHttpAdpter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new RiakHttpAdpter($this->client);
    }

    public function testCreateAdapterStrategy()
    {
        $get    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new GetRequest()]);
        $put    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new PutRequest()]);
        $delete = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new DeleteRequest()]);

        $this->assertInstanceOf('Basho\Riak\Core\Adapter\Http\Kv\HttpGet', $get);
        $this->assertInstanceOf('Basho\Riak\Core\Adapter\Http\Kv\HttpPut', $put);
        $this->assertInstanceOf('Basho\Riak\Core\Adapter\Http\Kv\HttpDelete', $delete);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnknownMessageException()
    {
        $mock = $this->getMock('Basho\Riak\Core\Message\Request');

        $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [$mock]);
    }
}
<?php

namespace BashoRiakTest\Core;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Message\Kv\GetRequest;
use Basho\Riak\Core\Message\Kv\PutRequest;
use Basho\Riak\Core\Message\Kv\DeleteRequest;
use Basho\Riak\Core\RiakProtoAdpter;

class RiakProtoAdpterTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Adapter\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\RiakProtoAdpter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Basho\Riak\Core\Adapter\Proto\ProtoClient', [], [], '', false);
        $this->instance = new RiakProtoAdpter($this->client);
    }

    public function testCreateAdapterStrategy()
    {
        $kvGet    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new GetRequest()]);
        $kvPut    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new PutRequest()]);
        $kvDelete = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new DeleteRequest()]);

        $this->assertInstanceOf('Basho\Riak\Core\Adapter\Proto\Kv\ProtoGet', $kvGet);
        $this->assertInstanceOf('Basho\Riak\Core\Adapter\Proto\Kv\ProtoPut', $kvPut);
        $this->assertInstanceOf('Basho\Riak\Core\Adapter\Proto\Kv\ProtoDelete', $kvDelete);
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
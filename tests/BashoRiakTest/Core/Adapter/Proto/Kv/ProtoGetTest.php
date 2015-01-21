<?php

namespace BashoRiakTest\Core\Adapter\Proto\Kv;

use BashoRiakTest\TestCase;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Proto\Kv\ProtoGet;
use Basho\Riak\Core\Message\Kv\GetRequest;

class ProtoGetTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Adapter\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\Adapter\Proto\Kv\ProtoGet
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Basho\Riak\Core\Adapter\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoGet($this->client);
    }

    public function testCreateGetProtoMessage()
    {
        $getRequest = new GetRequest();

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';
        $getRequest->key    = '1';

        $getRequest->r           = 3;
        $getRequest->pr          = 2;
        $getRequest->basicQuorum = true;
        $getRequest->notfoundOk  = true;

        $result = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\RpbGetReq', $result);
        $this->assertEquals('test_bucket', $result->bucket);
        $this->assertEquals('default', $result->type);
        $this->assertEquals('1', $result->key);

        $this->assertEquals(3, $result->r);
        $this->assertEquals(2, $result->pr);
        $this->assertEquals(true, $result->basic_quorum);
        $this->assertEquals(true, $result->notfound_ok);
    }

    public function testSendGetMessage()
    {
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Basho\Riak\ProtoBuf\RpbGetReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->callback($callback), $this->equalTo(RiakMessageCodes::GET_REQ), $this->equalTo(RiakMessageCodes::GET_RESP));

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->assertInstanceOf('Basho\Riak\Core\Message\Kv\GetResponse', $this->instance->send($request));
    }
}
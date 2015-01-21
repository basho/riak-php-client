<?php

namespace BashoRiakTest\Core\Adapter\Proto\Kv;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Message\Kv\Content;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Proto\Kv\ProtoPut;
use Basho\Riak\Core\Message\Kv\PutRequest;

class ProtoPutTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Adapter\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\Adapter\Proto\Kv\ProtoPut
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Basho\Riak\Core\Adapter\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoPut($this->client);
    }

    public function testCreatePutProtoMessage()
    {
        $content    = new Content();
        $putRequest = new PutRequest();

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';
        $putRequest->key    = '1';

        $putRequest->w           = 3;
        $putRequest->pw          = 2;
        $putRequest->dw          = 1;
        $putRequest->returnBody  = true;
        $putRequest->content     = $content;
        $putRequest->vClock      = 'vclock-hash';

        $content->contentType = 'application/json';
        $content->value       = '[1,1,1]';

        $result = $this->invokeMethod($this->instance, 'createRpbMessage', [$putRequest]);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\RpbPutReq', $result);
        $this->assertEquals('test_bucket', $result->bucket);
        $this->assertEquals('default', $result->type);
        $this->assertEquals('1', $result->key);

        $this->assertEquals(3, $result->w);
        $this->assertEquals(2, $result->pw);
        $this->assertEquals(1, $result->dw);
        $this->assertEquals(true, $result->return_body);
        $this->assertEquals('[1,1,1]', $result->content->value);
        $this->assertEquals('application/json', $result->content->content_type);
    }

    public function testSendPutMessage()
    {
        $request  = new PutRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Basho\Riak\ProtoBuf\RpbPutReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->callback($callback), $this->equalTo(RiakMessageCodes::PUT_REQ), $this->equalTo(RiakMessageCodes::PUT_RESP));

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->assertInstanceOf('Basho\Riak\Core\Message\Kv\PutResponse', $this->instance->send($request));
    }
}
<?php

namespace BashoRiakTest\Core\Adapter\Proto;

use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\ProtoBuf\RpbErrorResp;
use DrSlump\Protobuf\Protobuf;
use BashoRiakTest\TestCase;

class ProtoClientTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Adapter\Proto\ProtoClient
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = $this->getMock('Basho\Riak\Core\Adapter\Proto\ProtoClient', [], [], '', false);
    }

    /**
     * @expectedException Basho\Riak\RiakException
     * @expectedExceptionMessage Some Riak Error
     * @expectedExceptionCode -10
     */
    public function testThrowResponseException()
    {
        $message = new RpbErrorResp();

        $message->setErrmsg('Some Riak Error');
        $message->setErrcode(-10);

        $this->invokeMethod($this->instance, 'throwResponseException', [0, Protobuf::encode($message)]);
    }

    public function testClassForCode()
    {
        $this->assertEquals('Basho\Riak\ProtoBuf\DtFetchResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::DT_FETCH_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\DtUpdateResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::DT_UPDATE_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbErrorResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::ERROR_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbGetBucketResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::GET_BUCKET_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbErrorResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::ERROR_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbGetResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::GET_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbGetServerInfoResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::GET_SERVER_INFO_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbListBucketsResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::LIST_BUCKETS_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbListKeysResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::LIST_KEYS_RESP]));
        $this->assertEquals('Basho\Riak\ProtoBuf\RpbPutResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::PUT_RESP]));

        $this->assertNull($this->invokeMethod($this->instance, 'classForCode', [-100]));
    }
}
<?php

namespace BashoRiakTest\Core\Adapter\Http\Kv;

use BashoRiakTest\TestCase;
use GuzzleHttp\Stream\Stream;
use Basho\Riak\Core\Adapter\Http\Kv\HttpDelete;
use Basho\Riak\Core\Message\Kv\DeleteRequest;

class HttpDeleteTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\Adapter\Http\Kv\HttpDelete
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpDelete($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
        $this->assertArrayHasKey(204, $codes);
        $this->assertArrayHasKey(404, $codes);
    }

    public function testCreateDeleteHttpRequest()
    {
        $deleteRequest = new DeleteRequest();
        $url        = '/types/default/buckets/test_bucket/keys/1';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $deleteRequest->bucket = 'test_bucket';
        $deleteRequest->type   = 'default';
        $deleteRequest->key    = '1';

        $deleteRequest->r  = 1;
        $deleteRequest->pr = 2;
        $deleteRequest->rw = 3;
        $deleteRequest->w  = 3;
        $deleteRequest->dw = 2;
        $deleteRequest->pw = 1;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->exactly(6))
            ->method('add')
            ->will($this->returnValueMap([
                ['r', 1, $query],
                ['pr', 2, $query],
                ['rw', 3, $query],
                ['w', 3, $query],
                ['dw', 2, $query],
                ['pw', 1, $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$deleteRequest]));
    }

    public function testSendDeleteRequest()
    {
        $request      = new DeleteRequest();
        $stream       = Stream::factory('');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json',
                'Content-Length' => '0',
            ]);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Basho\Riak\Core\Message\Kv\DeleteResponse', $response);
    }
}
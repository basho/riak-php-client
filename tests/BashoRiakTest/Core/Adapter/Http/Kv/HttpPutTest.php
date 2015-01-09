<?php

namespace BashoRiakTest\Core\Adapter\Http\Kv;

use BashoRiakTest\TestCase;
use GuzzleHttp\Stream\Stream;
use Basho\Riak\Core\Adapter\Http\Kv\HttpPut;
use Basho\Riak\Core\Message\Kv\PutRequest;

class HttpPutTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\Adapter\Http\Kv\HttpPut
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpPut($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
        $this->assertArrayHasKey(201, $codes);
        $this->assertArrayHasKey(204, $codes);
        $this->assertArrayHasKey(300, $codes);
    }

    public function testCreateHttpPutRequest()
    {
        $putRequest = new PutRequest();
        $url        = '/types/default/buckets/test_bucket/keys/1';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';
        $putRequest->key    = '1';

        $putRequest->w           = 3;
        $putRequest->pw          = 2;
        $putRequest->dw          = 1;
        $putRequest->returnBody  = true;
        $putRequest->vClock      = 'vclock-hash';
        $putRequest->content     = [
            'contentType' => 'application/json',
            'value'       => '[1,1,1]'
        ];

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->exactly(3))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', ['multipart/mixed', '*/*'], $query],
                ['Content-Type', 'application/json', $query],
                ['X-Riak-Vclock', 'vclock-hash', $query]
            ]));

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $request->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('[1,1,1]'));

        $query->expects($this->exactly(4))
            ->method('add')
            ->will($this->returnValueMap([
                ['w', 1, $query],
                ['dw', 3, $query],
                ['pw', 2, $query],
                ['returnbody', 'true', $query]
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$putRequest]));
    }

    public function testGetRequestContent()
    {
        $putRequest   = new PutRequest();
        $stream       = Stream::factory('[1,1,1]');
        $query        = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $httpResponse->expects($this->once())
            ->method('getHeader')
            ->with($this->equalTo('X-Riak-Vclock'))
            ->willReturn('vclock-hash');

        $httpRequest->expects($this->exactly(3))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', ['multipart/mixed', '*/*'], $query],
                ['Content-Type', 'application/json', $query],
                ['X-Riak-Vclock', 'vclock-hash', $query]
            ]));

        $httpRequest->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('[1,1,1]'));

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json',
                'Last-Modified' => 'Sat, 03 Jan 2015 01:46:34 GMT',
            ]);

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';
        $putRequest->key    = '1';

        $putRequest->returnBody  = true;
        $putRequest->vClock      = 'vclock-hash';
        $putRequest->content     = [
            'contentType' => 'application/json',
            'value'       => '[1,1,1]'
        ];

        $response = $this->instance->send($putRequest);

        $this->assertInstanceOf('Basho\Riak\Core\Message\Kv\PutResponse', $response);
        $this->assertEquals('vclock-hash', $response->vClock);
        $this->assertCount(1, $response->contentList);

        $this->assertEquals('[1,1,1]', $response->contentList[0]['value']);
        $this->assertEquals('application/json', $response->contentList[0]['contentType']);
        $this->assertEquals('Sat, 03 Jan 2015 01:46:34 GMT', $response->contentList[0]['lastModified']);
    }
}
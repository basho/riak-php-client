<?php

namespace BashoRiakTest\Core\Adapter\Http\Kv;

use BashoRiakTest\TestCase;
use GuzzleHttp\Stream\Stream;
use Basho\Riak\Core\Adapter\Http\Kv\HttpGet;
use Basho\Riak\Core\Message\Kv\GetRequest;
use GuzzleHttp\Exception\ClientException;

class HttpGetTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\Adapter\Http\Kv\HttpGet
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpGet($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
        $this->assertArrayHasKey(300, $codes);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new GetRequest();
        $url        = '/types/default/buckets/test_bucket/keys/1';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';
        $getRequest->key    = '1';

        $getRequest->r           = 3;
        $getRequest->pr          = 3;
        $getRequest->basicQuorum = true;
        $getRequest->notfoundOk  = true;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->once())
            ->method('setHeader')
            ->with(
                $this->equalTo('Accept'),
                $this->equalTo(['multipart/mixed', '*/*'])
            );

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->exactly(4))
            ->method('add')
            ->will($this->returnValueMap([
                ['r', 3, $query],
                ['pr', 3, $query],
                ['basic_quorum', 'true', $query],
                ['notfound_ok', 'true', $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testGetRequestContent()
    {
        $request      = new GetRequest();
        $stream       = Stream::factory('[1,1,1]');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
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

        $httpResponse->expects($this->once())
            ->method('getHeader')
            ->with($this->equalTo('X-Riak-Vclock'))
            ->willReturn('vclock-hash');

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json',
                'Last-Modified' => 'Sat, 03 Jan 2015 01:46:34 GMT',
            ]);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Basho\Riak\Core\Message\Kv\GetResponse', $response);
        $this->assertEquals('vclock-hash', $response->vClock);
        $this->assertCount(1, $response->contentList);

        $this->assertEquals('[1,1,1]', $response->contentList[0]->value);
        $this->assertEquals('application/json', $response->contentList[0]->contentType);
        $this->assertEquals('Sat, 03 Jan 2015 01:46:34 GMT', $response->contentList[0]->lastModified);
    }

    public function testGetRequestHandl404ExceptioThrownByGuzzle()
    {
        $request      = new GetRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $httpQuery    = $this->getMock('GuzzleHttp\Query');

        $request->notfoundOk  = true;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
            ->willReturn($httpRequest);

        $httpResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willThrowException(new ClientException('Not Found', $httpRequest, $httpResponse));

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Basho\Riak\Core\Message\Kv\GetResponse', $response);
        $this->assertEmpty($response->contentList);
        $this->assertNull($response->vClock);
    }
}
<?php

namespace BashoRiakTest\Core\Adapter\Http\Kv;

use BashoRiakTest\TestCase;
use GuzzleHttp\Stream\Stream;

class BaseHttpStrategyTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\Adapter\Http\Kv\BaseHttpStrategy
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = $this->getMockForAbstractClass('Basho\Riak\Core\Adapter\Http\Kv\BaseHttpStrategy', [$this->client]);
    }

    public function testBuildPath()
    {
        $this->assertEquals('/types/type/buckets/bucket/keys/key', $this->invokeMethod($this->instance, 'buildPath', ['type', 'bucket', 'key']));
        $this->assertEquals('/buckets/bucket/keys/key', $this->invokeMethod($this->instance, 'buildPath', [null, 'bucket', 'key']));
    }

    public function testCreateRequest()
    {
        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/type/buckets/bucket/keys/key'));

        $this->invokeMethod($this->instance, 'createRequest', ['GET' , 'type', 'bucket', 'key']);
    }

    public function testParseSimpleHeaders()
    {
        $headers = $this->invokeMethod($this->instance, 'parseHeaders', [[
            'Content-Type'  => 'applications/json',
            'Last-Modified' => 'Fri, 02 Jan 2015 20:09:37 GMT'
        ]]);

        $this->assertEquals( [
            'contentType'  => 'applications/json',
            'lastModified' => 'Fri, 02 Jan 2015 20:09:37 GMT'
        ], $headers);
    }

    public function testGetRiakContentList()
    {
        $glue  = "\r\n";
        $part1 = implode($glue,[
            'Content-Type: application/json',
            'Link: </buckets/test_bucket>; rel="up"',
            'Etag: 5QqlA6qFh2Z88mxEDN5edh',
            'Last-Modified: Fri, 02 Jan 2015 20:09:37 GMT',
            '',
            '[1,1,1]'
        ]);

        $part2 = implode($glue,[
            'Content-Type: application/json',
            'Link: </buckets/test_bucket>; rel="up"',
            'Etag: 3JmLc4m8r37FL6R89fJoJr',
            'Last-Modified: Fri, 02 Jan 2015 20:09:44 GMT',
            '',
            '[2,2,2]'
        ]);

        $content = implode($glue, array_merge(
            [''],
            ['--KQLFjHN3yt2P0CWSxcIywUeI0kR'],
            [$part1],
            ['--KQLFjHN3yt2P0CWSxcIywUeI0kR'],
            [$part2],
            ['--KQLFjHN3yt2P0CWSxcIywUeI0kR--'],
            ['']
        ));

        $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $stream   = Stream::factory($content);

        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(300);

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $response->method('getHeader')
            ->will($this->returnValueMap([
                ['X-Riak-Vclock', 'vclock-hash'],
                ['Content-Type', 'multipart/mixed; boundary=KQLFjHN3yt2P0CWSxcIywUeI0kR']
            ]));

        $contentList = $this->invokeMethod($this->instance, 'getRiakContentList', [$response]);

        $this->assertCount(2, $contentList);
        $this->assertEquals('[1,1,1]', $contentList[0]['value']);
        $this->assertEquals('[2,2,2]', $contentList[1]['value']);
        $this->assertEquals('application/json', $contentList[0]['contentType']);
        $this->assertEquals('application/json', $contentList[1]['contentType']);
        $this->assertEquals('Fri, 02 Jan 2015 20:09:37 GMT', $contentList[0]['lastModified']);
        $this->assertEquals('Fri, 02 Jan 2015 20:09:44 GMT', $contentList[1]['lastModified']);
    }
}
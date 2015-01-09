<?php

namespace BashoRiakTest\Command\Kv;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Command\Kv\StoreValue;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Message\Kv\PutResponse;

class StoreValueTest extends TestCase
{
    private $location;
    private $client;
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->location = new RiakLocation(new RiakNamespace('bucket', 'type'), 'key');
        $this->adapter  = $this->getMock('Basho\Riak\Core\RiakAdapter');
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testStore()
    {
        $riakObject  = new RiakObject();
        $putResponse = new PutResponse();
        $command     = StoreValue::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withLocation($this->location)
            ->withValue($riakObject)
            ->build();

        $riakObject->setContentType('application/json');
        $riakObject->setValue('2,2,2]');

        $putResponse->vClock = 'vclock-hash';
        $putResponse->contentList = [
            [
                'lastModified'  => 'Sat, 01 Jan 2015 01:01:01 GMT',
                'contentType'   => 'application/json',
                'value'         => '[1,1,1]'
            ],
            [
                'lastModified'  => 'Sat, 02 Jan 2015 02:02:02 GMT',
                'contentType'   => 'application/json',
                'value'         => '[2,2,2]'
            ]
        ];

        $this->adapter->expects($this->once())
            ->method('send')
            ->will($this->returnValue($putResponse));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\StoreValueResponse', $result);
        $this->assertInstanceOf('Basho\Riak\Cap\VClock', $result->getVectorClock());

        $this->assertTrue($result->hasValues());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals(2, $result->getNumberOfValues());
        $this->assertEquals('vclock-hash', $result->getVectorClock()->getValue());

        $values = $result->getValues();

        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $values[0]);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $values[1]);
        $this->assertEquals('Sat, 01 Jan 2015 01:01:01 GMT', $values[0]->getLastModified());
        $this->assertEquals('Sat, 02 Jan 2015 02:02:02 GMT', $values[1]->getLastModified());
        $this->assertEquals('application/json', $values[0]->getContentType());
        $this->assertEquals('application/json', $values[1]->getContentType());
        $this->assertEquals('[1,1,1]', $values[0]->getValue());
        $this->assertEquals('[2,2,2]', $values[1]->getValue());
    }
}
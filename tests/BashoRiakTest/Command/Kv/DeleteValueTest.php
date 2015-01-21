<?php

namespace BashoRiakTest\Command\Kv;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Command\Kv\DeleteValue;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Message\Kv\DeleteResponse;

class DeleteValueTest extends TestCase
{
    private $vClock;
    private $location;
    private $client;
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->location = new RiakLocation(new RiakNamespace('bucket', 'type'), 'key');
        $this->adapter  = $this->getMock('Basho\Riak\Core\RiakAdapter');
        $this->vClock   = $this->getMock('Basho\Riak\Cap\VClock', [], ['hash']);
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testDelete()
    {
        $deleteResponse = new DeleteResponse();
        $command        = DeleteValue::builder()
            ->withOption(RiakOption::PR, 3)
            ->withLocation($this->location)
            ->withVClock($this->vClock)
            ->build();

        $this->adapter->expects($this->once())
            ->method('send')
            ->will($this->returnValue($deleteResponse));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\DeleteValueResponse', $result);

        $this->assertFalse($result->hasValues());
        $this->assertCount(0, $result->getValues());
        $this->assertEquals(0, $result->getNumberOfValues());
    }
}
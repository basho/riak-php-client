<?php

namespace BashoRiakTest\Command\DataType;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\StoreCounter;

class StoreCounterTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Query\RiakNamespace
     */
    private $location;

    /**
     * @var \Basho\Riak\RiakClient
     */
    private $client;

    /**
     * @var \Basho\Riak\Core\RiakAdapter
     */
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

    public function testBuildCommand()
    {
        $builder = StoreCounter::builder()
            ->withLocation($this->location)
            ->withOption(RiakOption::W, 1)
            ->withDelta(1);

        $this->assertInstanceOf('Basho\Riak\Command\DataType\StoreCounter', $builder->build());
    }
}
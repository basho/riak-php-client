<?php

namespace BashoRiakTest\Command\DataType;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\Crdt\RiakFlag;
use Basho\Riak\Core\Query\Crdt\RiakSet;
use Basho\Riak\Core\Query\Crdt\RiakMap;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Core\Query\Crdt\RiakRegister;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\StoreMap;

class StoreMapTest extends TestCase
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

    /**
     * @expectedException \Basho\Riak\RiakException
     * @expectedExceptionMessage Not implemented
     */
    public function testExecute()
    {
        $command = StoreMap::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->build();

        $this->client->execute($command);
    }

    public function testBuildCommand()
    {
        $command = StoreMap::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->build();

        $command->updateMap('map_key', [])
            ->updateSet('map_key', [])
            ->updateFlag('flag_key', true)
            ->updateCounter('map_counter', 1)
            ->updateRegister('map_register', 'foo');

        $command->removeMap('map_key')
            ->removeSet('map_key')
            ->removeFlag('flag_key')
            ->removeCounter('map_counter')
            ->removeRegister('map_register');

        $this->assertInstanceOf('Basho\Riak\Command\DataType\StoreMap', $command);
    }
}
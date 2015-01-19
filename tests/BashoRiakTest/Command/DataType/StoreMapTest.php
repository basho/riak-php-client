<?php

namespace BashoRiakTest\Command\DataType;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\StoreMap;
use Basho\Riak\Command\DataType\StoreSet;

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


    public function testBuildCommand()
    {
        $subMap = StoreMap::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->build();

        $subSet = StoreSet::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->build();

        $command = StoreMap::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->build();

        $command
            ->updateMap('map_key', $subMap)
            ->updateSet('set_key', $subSet)
            ->updateFlag('flag_key', true)
            ->updateCounter('map_counter', 1)
            ->updateRegister('map_register', 'foo');

        $command
            ->removeMap('map_key')
            ->removeSet('set_key')
            ->removeFlag('flag_key')
            ->removeCounter('map_counter')
            ->removeRegister('map_register');

        $this->assertInstanceOf('Basho\Riak\Command\DataType\StoreMap', $command);
    }
}
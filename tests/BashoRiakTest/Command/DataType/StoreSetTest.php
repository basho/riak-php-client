<?php

namespace BashoRiakTest\Command\DataType;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\Crdt\RiakSet;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\StoreSet;

class StoreSetTest extends TestCase
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
        $command = StoreSet::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->withSet(new RiakSet())
            ->build();

        $this->client->execute($command);
    }

    public function testBuildCommand()
    {
        $riakSet = new RiakSet();
        $builder = StoreSet::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->withSet($riakSet);

        $command = $builder->build();

        $command->add(new RiakCounter(1))
            ->remove(new RiakCounter(1))
            ->add(new RiakCounter(2));

        $this->assertInstanceOf('Basho\Riak\Command\DataType\StoreSet', $builder->build());
    }
}
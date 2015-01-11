<?php

namespace BashoRiakTest\Command\DataType;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakNode;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\RiakClientBuilder;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\FetchSet;

class FetchSetTest extends TestCase
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
        $command = FetchSet::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->build();

        $this->client->execute($command);
    }

    public function testBuildCommand()
    {
        $builder = FetchSet::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location);

        $this->assertInstanceOf('Basho\Riak\Command\DataType\FetchSet', $builder->build());
    }
}
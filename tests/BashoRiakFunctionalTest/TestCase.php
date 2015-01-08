<?php

namespace BashoRiakFunctionalTest;

use Basho\Riak\RiakClientBuilder;

abstract class TestCase extends \BashoRiakTest\TestCase
{
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        if (@fsockopen('localhost', 8098) === false) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to riak');
        }

        $builder = new RiakClientBuilder();
        $client  = $builder
            ->withNodeUri('http://localhost:8098')
            ->build();

        $this->client = $client;
    }
}

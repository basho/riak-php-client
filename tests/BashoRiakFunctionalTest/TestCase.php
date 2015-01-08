<?php

namespace BashoRiakFunctionalTest;

use Basho\Riak\RiakClientBuilder;

abstract class TestCase extends \BashoRiakTest\TestCase
{
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        if (@fsockopen('127.0.0.1', 8098) === false) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to riak');
        }

        $builder = new RiakClientBuilder();
        $client  = $builder
            ->withNodeUri('http://127.0.0.1:8098')
            ->build();

        $this->client = $client;
    }
}

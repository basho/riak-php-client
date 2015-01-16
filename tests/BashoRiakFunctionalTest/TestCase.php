<?php

namespace BashoRiakFunctionalTest;

use Basho\Riak\RiakClientBuilder;

abstract class TestCase extends \BashoRiakTest\TestCase
{
    protected $client;

    protected $nodeUri;

    protected function setUp()
    {
        parent::setUp();

        $nodeUri  = getenv('RIAK_NODE_URI') ?: 'http://127.0.0.1:8098';
        $nodeHost = parse_url($nodeUri, PHP_URL_HOST);
        $nodePort = parse_url($nodeUri, PHP_URL_PORT);

        if ((@fsockopen($nodeHost, $nodePort) === false)) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to riak : ' . $nodeUri);
        }

        $builder = new RiakClientBuilder();
        $client  = $builder
            ->withNodeUri($nodeUri)
            ->build();

        $this->client  = $client;
        $this->nodeUri = $nodeUri;
    }
}

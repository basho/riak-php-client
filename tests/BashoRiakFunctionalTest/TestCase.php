<?php

namespace BashoRiakFunctionalTest;

use Basho\Riak\RiakClientBuilder;

abstract class TestCase extends \BashoRiakTest\TestCase
{
    /**
     * @var \Basho\Riak\RiakClient
     */
    protected $client;

    /**
     * @return \Basho\Riak\RiakClient
     */
    abstract protected function createClient();

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createClient();
    }

    /**
     * @return \Basho\Riak\RiakClient
     */
    protected function createRiakProtoClient()
    {
        return $this->createRiakClient('proto://127.0.0.1:8087');
    }

    /**
     * @return \Basho\Riak\RiakClient
     */
    protected function createRiakHttpClient()
    {
        return $this->createRiakClient('http://127.0.0.1:8098');
    }

    /**
     * @param string $nodeUri
     *
     * @return \Basho\Riak\RiakClient
     */
    protected function createRiakClient($nodeUri)
    {
        $nodeHost = parse_url($nodeUri, PHP_URL_HOST);
        $nodePort = parse_url($nodeUri, PHP_URL_PORT);

        if ((@fsockopen($nodeHost, $nodePort) === false)) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to riak : ' . $nodeUri);
        }

        $builder = new RiakClientBuilder();
        $client  = $builder
            ->withNodeUri($nodeUri)
            ->build();

        return $client;
    }
}

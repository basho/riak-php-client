<?php

namespace BashoRiakFunctionalTest\Command\Kv;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Command\DataType\FetchCounter;
use Basho\Riak\Command\DataType\StoreCounter;

class CounterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $client  = new \GuzzleHttp\Client();
        $request = $client->createRequest('PUT', 'http://127.0.0.1:8098/types/counters/buckets/counters/props');

        $request->addHeader('Content-Type', 'application/json');
        $request->setBody(\GuzzleHttp\Stream\Stream::factory(json_encode([
            'props' => [
                'allow_mult' => true,
                'n_val'      => 3,
            ]
        ])));

        $client->send($request);
    }

    public function testFetchCounter()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('counters', 'counters'), $key);

        $store = StoreCounter::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withCounter(new RiakCounter(10))
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->build();

        $fetch = FetchCounter::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);
        $counter       = $fetchResponse->getDatatype();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\StoreCounterResponse', $storeResponse);
        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\FetchCounterResponse', $fetchResponse);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Crdt\RiakCounter', $counter);
        $this->assertEquals($location, $fetchResponse->getLocation());
        $this->assertEquals(10, $counter->getValue());
    }
}
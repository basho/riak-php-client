<?php

namespace BashoRiakFunctionalTest\Command\Kv;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\FetchCounter;

class CounterTest extends TestCase
{
    public function testFetchCounter()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('counters', 'counters'), $key);
        $fetch    = FetchCounter::builder()
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
            ->build();

        // use Guzzle for now
        $client  = new \GuzzleHttp\Client();
        $request = $client->createRequest('POST', 'http://localhost:8098/types/counters/buckets/counters/datatypes/' . $key);
        $request->addHeader('Content-Type', 'application/json');
        $request->setBody(\GuzzleHttp\Stream\Stream::factory('10'));
        $client->send($request);

        $fetchResponse = $this->client->execute($fetch);
        $counter       = $fetchResponse->getDatatype();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\FetchCounterResponse', $fetchResponse);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Crdt\RiakCounter', $counter);
        $this->assertEquals(10, $counter->getValue());
    }
}
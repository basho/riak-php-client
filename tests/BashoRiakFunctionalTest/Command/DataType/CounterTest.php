<?php

namespace BashoRiakFunctionalTest\Command\Kv;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\FetchCounter;
use Basho\Riak\Command\DataType\StoreCounter;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

/**
 * @group non-proto
 */
class CounterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('counters', 'counters');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testStoreAndFetchCounter()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('counters', 'counters'), $key);

        $store = StoreCounter::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->withDelta(10)
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
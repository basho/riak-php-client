<?php

namespace BashoRiakFunctionalTest\Command\DataType;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\FetchCounter;
use Basho\Riak\Command\DataType\StoreCounter;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

abstract class CounterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->execute(StoreBucketProperties::builder()
            ->withNamespace(new RiakNamespace('counters', 'counters'))
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->build());
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

        $fetchResponse1 = $this->client->execute($fetch);
        $storeResponse  = $this->client->execute($store);
        $fetchResponse2 = $this->client->execute($fetch);

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\FetchCounterResponse', $fetchResponse1);
        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\StoreCounterResponse', $storeResponse);
        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\FetchCounterResponse', $fetchResponse2);

        $this->assertNull($fetchResponse1->getDatatype());
        $this->assertInstanceOf('Basho\Riak\Core\Query\Crdt\RiakCounter', $fetchResponse2->getDatatype());

        $this->assertEquals($location, $fetchResponse2->getLocation());
        $this->assertEquals(10, $fetchResponse2->getDatatype()->getValue());
    }
}
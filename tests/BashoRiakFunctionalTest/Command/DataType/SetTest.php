<?php

namespace BashoRiakFunctionalTest\Command\Kv;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\FetchSet;
use Basho\Riak\Command\DataType\StoreSet;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Core\Query\Crdt\RiakCounter;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

class SetTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('sets', 'sets');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testStoreAndFetchSet()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('sets', 'sets'), $key);

        $store = StoreSet::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->add("Ottawa")
            ->add("Toronto")
            ->build();

        $fetch = FetchSet::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);
        $set           = $fetchResponse->getDatatype();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\StoreSetResponse', $storeResponse);
        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\FetchSetResponse', $fetchResponse);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Crdt\RiakSet', $set);
        $this->assertEquals($location, $fetchResponse->getLocation());
        $this->assertEquals(["Ottawa","Toronto"], $set->getValue());
    }
}
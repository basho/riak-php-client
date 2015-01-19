<?php

namespace BashoRiakFunctionalTest\Command\DataType;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Command\DataType\FetchMap;
use Basho\Riak\Command\DataType\StoreMap;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

abstract class MapTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('maps', 'maps');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testStoreAndFetchSimpleMap()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('maps', 'maps'), $key);

        $store = StoreMap::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->updateRegister('url', 'google.com')
            ->updateCounter('clicks', 100)
            ->updateFlag('active', true)
            ->build();

        $fetch = FetchMap::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\StoreMapResponse', $storeResponse);
        $this->assertInstanceOf('Basho\Riak\Command\DataType\Response\FetchMapResponse', $fetchResponse);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Crdt\RiakMap', $fetchResponse->getDatatype());

        $this->assertEquals('google.com', $fetchResponse->getDatatype()->get('url'));
        $this->assertEquals(100, $fetchResponse->getDatatype()->get('clicks'));
        $this->assertTrue($fetchResponse->getDatatype()->get('active'));
        $this->assertEquals($location, $fetchResponse->getLocation());
    }
}
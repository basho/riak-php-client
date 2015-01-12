<?php

namespace BashoRiakFunctionalTest\Command\Bucket;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Command\Bucket\FetchBucketProperties;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

class BucketPropertiesTest extends TestCase
{
    public function testFetchBucketProperties()
    {
        $namespace = new RiakNamespace('bucket', 'default');

        $store = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $fetch = FetchBucketProperties::builder()
            ->withNamespace($namespace)
            ->build();

        $storeResponse   = $this->client->execute($store);
        $fetchResponse   = $this->client->execute($fetch);
        $fetchProperties = $fetchResponse->getProperties();

        $this->assertInstanceOf('Basho\Riak\Command\Bucket\Response\StoreBucketPropertiesResponse', $storeResponse);
        $this->assertInstanceOf('Basho\Riak\Command\Bucket\Response\FetchBucketPropertiesResponse', $fetchResponse);
        $this->assertInstanceOf('Basho\Riak\Core\Query\BucketProperties', $fetchProperties);
        $this->assertTrue($fetchProperties->getAllowSiblings());
        $this->assertEquals(3, $fetchProperties->getNVal());
    }
}
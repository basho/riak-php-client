<?php

namespace BashoRiakFunctionalTest\Command\Kv;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Command\Kv\FetchValue;
use Basho\Riak\Command\Kv\StoreValue;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Command\Kv\DeleteValue;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Query\BucketProperties;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

class RiakObjectTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('buckets', 'default');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testStoreAndFetchSingleValue()
    {
        $key      = uniqid();
        $object   = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

        $object->setValue('[1,1,1]');
        $object->setContentType('application/json');

        $store = StoreValue::builder($location, $object)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
            ->build();

        $this->client->execute($store);

        $result     = $this->client->execute($fetch);
        $riakObject = $result->getValue();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('[1,1,1]', $riakObject->getValue());

        $this->client->execute(DeleteValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->build());
    }

    public function testStoreAndFetchValueWithSiblings()
    {
        $key      = uniqid();
        $object1  = new RiakObject();
        $object2  = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

        $object1->setValue('[1,1,1]');
        $object2->setValue('[2,2,2]');
        $object1->setContentType('application/json');
        $object2->setContentType('application/json');

        $store1 = StoreValue::builder($location, $object1)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $store2 = StoreValue::builder($location, $object2)
            ->withOption(RiakOption::W, 1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
            ->build();

        $delete  = DeleteValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->build();

        $resultFetch1  = $this->client->execute($fetch);
        $resultStore1  = $this->client->execute($store1);
        $resultStore2  = $this->client->execute($store2);
        $resultFetch2  = $this->client->execute($fetch);
        $resultDelete  = $this->client->execute($delete);
        $resultFetch3  = $this->client->execute($fetch);

        $this->assertTrue($resultFetch1->getNotFound());
        $this->assertFalse($resultFetch2->getNotFound());
        $this->assertTrue($resultFetch3->getNotFound());

        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\StoreValueResponse', $resultStore1);
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\StoreValueResponse', $resultStore2);
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $resultFetch2);
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $resultFetch3);
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\DeleteValueResponse', $resultDelete);

        $values = $resultFetch2->getValues();

        $this->assertCount(2, $values);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $values[0]);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $values[0]);
        $this->assertEquals('[1,1,1]', (string)$values[0]->getValue());
        $this->assertEquals('[2,2,2]', (string)$values[1]->getValue());
    }
}
<?php

namespace BashoRiakFunctionalTest\Command\Kv;

use BashoRiakFunctionalTest\TestCase;
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Command\Kv\FetchValue;
use Basho\Riak\Command\Kv\StoreValue;
use Basho\Riak\Command\Kv\DeleteValue;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;

class DomainObjectTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $client  = new \GuzzleHttp\Client();
        $request = $client->createRequest('PUT', 'http://127.0.0.1:8098/buckets/bucket/props');

        $request->addHeader('Content-Type', 'application/json');
        $request->setBody(\GuzzleHttp\Stream\Stream::factory(json_encode([
            'props' => [
                'allow_mult' => true,
                'n_val'      => 3,
            ]
        ])));

        $client->send($request);
    }

    public function testStoreAndFetchSingleValue()
    {
        $key      = uniqid();
        $object   = new SimpleObject('[1,1,1]');
        $location = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

        $store = StoreValue::builder($location, $object)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
            ->build();

        $this->client->execute($store);

        $result = $this->client->execute($fetch);
        $domain = $result->getValue(SimpleObject::CLASS_NAME);

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domain);
        $this->assertEquals('[1,1,1]', $domain->getValue());

        $this->client->execute(DeleteValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->build());
    }

    public function testStoreAndFetchValueWithSiblings()
    {
        $key      = uniqid();
        $object1  = new SimpleObject('[1,1,1]');
        $object2  = new SimpleObject('[2,2,2]');
        $location = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

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

        $values = $resultFetch2->getValues(SimpleObject::CLASS_NAME);

        $this->assertCount(2, $values);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $values[0]);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $values[1]);

        $this->assertEquals($key, $values[0]->getRiakKey());
        $this->assertEquals($key, $values[1]->getRiakKey());
        $this->assertEquals('default', $values[0]->getRiakBucketType());
        $this->assertEquals('default', $values[1]->getRiakBucketType());
        $this->assertEquals('bucket', $values[0]->getRiakBucketName());
        $this->assertEquals('bucket', $values[1]->getRiakBucketName());
        $this->assertEquals('[1,1,1]', $values[0]->getValue());
        $this->assertEquals('[2,2,2]', $values[1]->getValue());
    }
}
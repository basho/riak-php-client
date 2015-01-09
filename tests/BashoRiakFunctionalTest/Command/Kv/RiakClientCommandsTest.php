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

class RiakClientCommandsTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $client  = new \GuzzleHttp\Client();
        $request = $client->createRequest('PUT', 'http://127.0.0.1:8098/buckets/test_bucket/props');

        $request->addHeader('Content-Type', 'application/json');
        $request->setBody(\GuzzleHttp\Stream\Stream::factory(json_encode([
            'props' => [
                'allow_mult' => true,
                'n_val'      => 3,
            ]
        ])));

        $client->send($request);
    }

    public function testStoreAndFetchValueWithSiblings()
    {
        $key      = uniqid();
        $object1  = new SimpleObject('[1,1,1]');
        $object2  = new SimpleObject('[2,2,2]');
        $location = new RiakLocation(new RiakNamespace('test_bucket', 'default'), $key);

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

        $riakObjects   = $resultFetch2->getValues();
        $domainObjects = $resultFetch2->getValuesAs(SimpleObject::CLASS_NAME);

        $this->assertCount(2, $domainObjects);
        $this->assertCount(2, $riakObjects);

        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domainObjects[0]);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domainObjects[1]);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObjects[0]);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObjects[0]);

        $this->assertEquals($key, $domainObjects[0]->getRiakKey());
        $this->assertEquals($key, $domainObjects[1]->getRiakKey());
        $this->assertEquals('default', $domainObjects[0]->getRiakBucketType());
        $this->assertEquals('default', $domainObjects[1]->getRiakBucketType());
        $this->assertEquals('test_bucket', $domainObjects[0]->getRiakBucketName());
        $this->assertEquals('test_bucket', $domainObjects[1]->getRiakBucketName());

        $this->assertEquals('[1,1,1]', $domainObjects[0]->getValue());
        $this->assertEquals('[2,2,2]', $domainObjects[1]->getValue());

        $this->assertEquals('{"value":"[1,1,1]"}', $riakObjects[0]->getValue());
        $this->assertEquals('{"value":"[2,2,2]"}', $riakObjects[1]->getValue());
    }
}
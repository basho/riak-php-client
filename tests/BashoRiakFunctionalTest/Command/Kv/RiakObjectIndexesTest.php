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
use Basho\Riak\Core\Query\Index\RiakIndexBin;
use Basho\Riak\Core\Query\Index\RiakIndexInt;
use Basho\Riak\Core\Query\Index\RiakIndexList;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

class RiakObjectIndexesTest extends TestCase
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

    public function testStoreAndFetchSingleValueWithIndexes()
    {
        $key        = uniqid();
        $object     = new RiakObject();
        $indexes    = new RiakIndexList([]);
        $location   = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

        $indexes['key']   = new RiakIndexInt('key');
        $indexes['email'] = new RiakIndexBin('email');

        $indexes['key']->addValue(123);
        $indexes['email']->addValue('fabio.bat.silva@gmail.com');

        $object->setContentType('application/json');
        $object->setValue('{"name": "fabio"}');
        $object->setIndexes($indexes);

        $store = StoreValue::builder($location, $object)
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
            ->build();

        $this->client->execute($store);

        $result      = $this->client->execute($fetch);
        $riakObject  = $result->getValue();
        $riakIndexes = $riakObject->getIndexes();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Index\RiakIndexList', $riakIndexes);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"name": "fabio"}', $riakObject->getValue());

        $this->assertCount(2, $riakIndexes);
        $this->assertTrue(isset($riakIndexes['key']));
        $this->assertTrue(isset($riakIndexes['email']));

        $this->assertInstanceOf('Basho\Riak\Core\Query\Index\RiakIndexInt', $riakIndexes['key']);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Index\RiakIndexBin', $riakIndexes['email']);

        $this->assertEquals('key', $riakIndexes['key']->getName());
        $this->assertEquals('email', $riakIndexes['email']->getName());

        $this->assertEquals([123], $riakIndexes['key']->getValues());
        $this->assertEquals(['fabio.bat.silva@gmail.com'], $riakIndexes['email']->getValues());

        $this->client->execute(DeleteValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->build());
    }
}
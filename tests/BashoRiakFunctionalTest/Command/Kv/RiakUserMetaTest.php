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
use Basho\Riak\Core\Query\Meta\RiakUsermeta;
use Basho\Riak\Command\Bucket\StoreBucketProperties;

class RiakUserMetaTest extends TestCase
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

    public function testObjectWithUserMeta()
    {
        $key        = uniqid();
        $object     = new RiakObject();
        $meta       = new RiakUsermeta();
        $location   = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

        $meta['key']    = 'other';
        $meta['meta']   = 'content';
        $meta['remove'] = 'other';

        $meta->remove('remove');
        $meta->put('key', 'value');

        $object->setContentType('application/json');
        $object->setValue('{"name": "fabio"}');
        $object->setUserMeta($meta);

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

        $result     = $this->client->execute($fetch);
        $riakObject = $result->getValue();
        $riakMeta   = $riakObject->getUserMeta();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Meta\RiakUserMeta', $riakMeta);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"name": "fabio"}', $riakObject->getValue());

        $this->assertCount(2, $riakMeta);
        $this->assertTrue(isset($riakMeta['key']));
        $this->assertTrue(isset($riakMeta['meta']));
        $this->assertEquals('value', $riakMeta['key']);
        $this->assertEquals('content', $riakMeta['meta']);
        $this->assertEquals('value', $riakMeta->get('key'));
        $this->assertEquals('content', $riakMeta->get('meta'));

        $this->client->execute(DeleteValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->build());
    }

    public function testSiblingsWithUserMeta()
    {
        $key      = uniqid();
        $object1  = new RiakObject();
        $object2  = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('bucket', 'default'), $key);

        $object1->setContentType('application/json');
        $object1->setValue('{"name": "fabio"}');
        $object1->addMeta('group', 'guest');

        $object2->setContentType('application/json');
        $object2->setValue('{"name": "fabio"}');
        $object2->addMeta('group', 'admin');

        $this->client->execute(StoreValue::builder($location, $object1)
            ->withOption(RiakOption::W, 3)
            ->build());

        $this->client->execute(StoreValue::builder($location, $object2)
            ->withOption(RiakOption::W, 3)
            ->build());

        $result = $this->client->execute(FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
            ->build());

        $this->assertInstanceOf('Basho\Riak\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertCount(2, $result->getValues());

        $riakObject1 = $result->getValues()->offsetGet(0);
        $riakObject2 = $result->getValues()->offsetGet(1);
        $riakMeta1   = $riakObject1->getUserMeta();
        $riakMeta2   = $riakObject2->getUserMeta();

        $this->assertInstanceOf('Basho\Riak\Core\Query\Meta\RiakUserMeta', $riakMeta1);
        $this->assertInstanceOf('Basho\Riak\Core\Query\Meta\RiakUserMeta', $riakMeta2);

        $this->assertCount(1, $riakMeta1);
        $this->assertTrue(isset($riakMeta1['group']));
        $this->assertTrue(isset($riakMeta2['group']));
        $this->assertEquals('guest', $riakMeta1['group']);
        $this->assertEquals('admin', $riakMeta2['group']);

        $this->client->execute(DeleteValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->build());
    }
}
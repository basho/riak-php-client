<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Functional tests related to Key-Value objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ObjectOperationsTest extends TestCase
{
    private static $key = '';

    /**
     * @var \Basho\Riak\Object|null
     */
    private static $object = NULL;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make completely random key based on time
        static::$key = md5(rand(0, 99) . time());
    }

    public function testStoreNewWithoutKey()
    {
        // build an object
        $command = (new Command\Builder\StoreObject(static::$riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getCode());
        $this->assertNotEmpty($response->getLocation());
        $this->assertInstanceOf('\Basho\Riak\Location', $response->getLocation());
    }

    public function testFetchNotFound()
    {
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode());
    }

    /**
     * @depends      testFetchNotFound
     */
    public function testStoreNewWithKey()
    {
        $command = (new Command\Builder\StoreObject(static::$riak))
            ->buildObject('some_data')
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getCode());
        $this->assertEmpty($response->getLocation());
    }

    /**
     * @depends      testStoreNewWithKey
     */
    public function testFetchOk()
    {
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals('some_data', $response->getObject()->getData());
        $this->assertNotEmpty($response->getObject()->getVclock());

        static::$object = $response->getObject();
    }

    /**
     * @depends      testFetchOk
     */
    public function testStoreExisting()
    {
        $object = static::$object;

        $object->setData('some_new_data');

        $command = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testStoreExisting
     */
    public function testDelete()
    {
        $command = (new Command\Builder\DeleteObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testDelete
     */
    public function testFetchDeleted()
    {
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode());

        // deleted keys leave behind a tombstone with their causal context, aka vclock unless delete_immediate = 1
        //$this->assertNotEmpty($response->getVclock());
    }

    public function testFetchAssociativeArray()
    {
        $data = ['myData' => 42];

        $command = (new Command\Builder\StoreObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->buildJsonObject($data)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());

        // Fetch as associative array
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->withDecodeAsAssociative()
            ->build();

        $response = $command->execute();
        $this->assertEquals('200', $response->getCode());
        $this->assertEquals($data, $response->getObject()->getData());
        $this->assertEquals('array', gettype($response->getObject()->getData()));

        // Fetch normal to get as stdClass object
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();
        $this->assertEquals('200', $response->getCode());
        $this->assertEquals('object', gettype($response->getObject()->getData()));
    }
}

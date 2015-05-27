<?php

/*
Copyright 2015 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

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
        // make completely random key based on time
        static::$key = md5(rand(0, 99) . time());
    }

    /**
     * @dataProvider getLocalNodeConnection
     * @param $riak \Basho\Riak
     */
    public function testStoreNewWithoutKey($riak)
    {
        // build an object
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getStatusCode());
        $this->assertNotEmpty($response->getLocation());
        $this->assertInstanceOf('\Basho\Riak\Location', $response->getLocation());
    }

    /**
     * @dataProvider getLocalNodeConnection
     * @param $riak \Basho\Riak
     */
    public function testFetchNotFound($riak)
    {
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getStatusCode());
    }

    /**
     * This test expects an exception on retrieval of Location, since a store with a key won't have it.
     *
     * @depends      testFetchNotFound
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Exception
     */
    public function testStoreNewWithKey($riak)
    {
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getStatusCode());
        $this->assertEmpty($response->getLocation());
    }

    /**
     * @depends      testStoreNewWithKey
     * @dataProvider getLocalNodeConnection
     * @param $riak \Basho\Riak
     */
    public function testFetchOk($riak)
    {
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals('some_data', $response->getObject()->getData());
        $this->assertNotEmpty($response->getVClock());

        static::$object = $response->getObject();
    }

    /**
     * @depends      testFetchOk
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreExisting($riak)
    {
        $object = static::$object;

        $object->setData('some_new_data');

        $command = (new Command\Builder\StoreObject($riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testStoreExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testDelete($riak)
    {
        $command = (new Command\Builder\DeleteObject($riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testDelete
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchDeleted($riak)
    {
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getStatusCode());

        // deleted key's still leave behind a tombstone with their causal context, aka vclock
        $this->assertNotEmpty($response->getVclock());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchAssociativeArray($riak)
    {
        $data = ['myData' => 42];

        $command = (new Command\Builder\StoreObject($riak))
            ->buildLocation(static::$key, 'users')
            ->buildJsonObject($data)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());

        // Fetch as associative array
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'users')
            ->withDecodeAsAssociative()
            ->build();

        $response = $command->execute();
        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals($data, $response->getObject()->getData());
        $this->assertEquals('array', gettype($response->getObject()->getData()));

        // Fetch normal to get as stdClass object
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'users')
            ->build();

        $response = $command->execute();
        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals('object', gettype($response->getObject()->getData()));
    }
}
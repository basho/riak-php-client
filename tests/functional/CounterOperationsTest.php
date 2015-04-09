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
 * Class CounterTest
 *
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class CounterOperationsTest extends TestCase
{
    private static $key = '';

    public static function setUpBeforeClass()
    {
        // make completely random key based on time
        static::$key = md5(rand(0, 99) . time());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testIncrementNewWithoutKey($riak)
    {
        // build an object
        $command = (new Command\Builder\IncrementCounter($riak))
            ->withIncrement(1)
            ->buildBucket('visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getStatusCode());
        $this->assertNotEmpty($response->getLocation());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchNotFound($riak)
    {
        $command = (new Command\Builder\FetchCounter($riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getStatusCode());
    }

    /**
     * @depends      testFetchNotFound
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Exception
     */
    public function testIncrementNewWithKey($riak)
    {
        $command = (new Command\Builder\IncrementCounter($riak))
            ->withIncrement(1)
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getStatusCode());
        $this->assertEmpty($response->getLocation());
    }

    /**
     * @depends      testIncrementNewWithKey
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk($riak)
    {
        $command = (new Command\Builder\FetchCounter($riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $response->getCounter());
        $this->assertNotEmpty($response->getCounter()->getData());
        $this->assertTrue(is_integer($response->getCounter()->getData()));
        $this->assertEquals(1, $response->getCounter()->getData());
    }

    /**
     * @depends      testFetchOk
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testIncrementExisting($riak)
    {
        $command = (new Command\Builder\IncrementCounter($riak))
            ->withIncrement(1)
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testIncrementExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk2($riak)
    {
        $command = (new Command\Builder\FetchCounter($riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $response->getCounter());
        $this->assertNotEmpty($response->getCounter()->getData());
        $this->assertTrue(is_integer($response->getCounter()->getData()));
        $this->assertEquals(2, $response->getCounter()->getData());
    }

    /**
     * @depends      testFetchOk
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testDecrementExisting($riak)
    {
        $command = (new Command\Builder\IncrementCounter($riak))
            ->withIncrement(-1)
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testDecrementExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk3($riak)
    {
        $command = (new Command\Builder\FetchCounter($riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $response->getCounter());
        $this->assertNotEmpty($response->getCounter()->getData());
        $this->assertTrue(is_integer($response->getCounter()->getData()));
        $this->assertEquals(1, $response->getCounter()->getData());
    }

}
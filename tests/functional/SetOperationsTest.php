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
 * Functional tests related to Set CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SetTest extends TestCase
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
    public function testAddWithoutKey($riak)
    {
        // build an object
        $command = (new Command\Builder\UpdateSet($riak))
            ->add('gosabres poked you.')
            ->add('phprocks viewed your profile.')
            ->add('phprocks started following you.')
            ->buildBucket('default', static::SET_BUCKET_TYPE)
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
        $command = (new Command\Builder\FetchSet($riak))
            ->buildLocation(static::$key, 'default', static::SET_BUCKET_TYPE)
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
    public function testAddNewWithKey($riak)
    {
        $command = (new Command\Builder\UpdateSet($riak))
            ->add('Sabres')
            ->add('Canadiens')
            ->add('Bruins')
            ->add('Maple Leafs')
            ->add('Senators')
            ->add('Red Wings')
            ->add('Thrashers')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getStatusCode());
        $this->assertEmpty($response->getLocation());
    }

    /**
     * @depends      testAddNewWithKey
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk($riak)
    {
        $command = (new Command\Builder\FetchSet($riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));
    }

    /**
     * @depends      testFetchOk
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testAddExisting($riak)
    {
        $command = (new Command\Builder\UpdateSet($riak))
            ->add('Lightning')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testAddExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk2($riak)
    {
        $command = (new Command\Builder\FetchSet($riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(8, count($response->getSet()->getData()));
    }

    /**
     * @depends      testFetchOk
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testRemoveExisting($riak)
    {
        $command = (new Command\Builder\UpdateSet($riak))
            ->remove('Thrashers')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testRemoveExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk3($riak)
    {
        $command = (new Command\Builder\FetchSet($riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));
    }

    /**
     * @depends      testFetchOk
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testAddRemoveExisting($riak)
    {
        $command = (new Command\Builder\UpdateSet($riak))
            ->add('Penguins')
            ->add('Ducks')
            ->remove('Lightning')
            ->remove('Red Wings')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testRemoveExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchOk4($riak)
    {
        $command = (new Command\Builder\FetchSet($riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));
        $this->assertTrue(in_array('Ducks', $response->getSet()->getData()));
        $this->assertFalse(in_array('Lightning', $response->getSet()->getData()));
    }
}
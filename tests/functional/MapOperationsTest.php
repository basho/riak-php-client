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
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class MapOperationsTest extends TestCase
{
    /**
     * Key to be used for tests
     *
     * @var string
     */
    private static $key = '';

    /**
     * Array of context generated from working with the same Set
     *
     * @var array
     */
    private static $context = [];

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
        // build a map update command
        $command = (new Command\Builder\UpdateMap($riak))
            ->updateRegister('favorite', 'Buffalo Sabres')
            ->buildBucket('default', static::MAP_BUCKET_TYPE)
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
        $command = (new Command\Builder\FetchMap($riak))
            ->buildLocation(static::$key, 'default', static::MAP_BUCKET_TYPE)
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
        $updateSetBuilder = (new Command\Builder\UpdateSet($riak))
            ->add('Sabres');

        $updateCounterBuilder = (new Command\Builder\IncrementCounter($riak))
            ->withIncrement(1);

        $command = (new Command\Builder\UpdateMap($riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->updateCounter('teams', $updateCounterBuilder)
            ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getStatusCode());
        $this->assertEmpty($response->getLocation());

        $command = (new Command\Builder\FetchMap($riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $map = $response->getMap();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Set', $map->getSet('ATLANTIC_DIVISION'));
        $this->assertEquals(1, count($map->getSet('ATLANTIC_DIVISION')->getData()));

        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $map->getCounter('teams'));
        $this->assertEquals(1, $map->getCounter('teams')->getData());
        $this->assertNotEmpty($map->getContext());

        static::$context[] = $response->getMap()->getContext();
    }

    /**
     * @depends      testAddNewWithKey
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testAddExisting($riak)
    {
        $updateSetBuilder = (new Command\Builder\UpdateSet($riak))
            ->add('Bruins')
            ->add('Thrashers');

        $updateCounterBuilder = (new Command\Builder\IncrementCounter($riak))
            ->withIncrement(2);

        // build a map update command
        $command = (new Command\Builder\UpdateMap($riak))
            ->updateFlag('expansion_year', TRUE)
            ->updateCounter('teams', $updateCounterBuilder)
            ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());

        $command = (new Command\Builder\FetchMap($riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $map = $response->getMap();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Set', $map->getSet('ATLANTIC_DIVISION'));
        $this->assertEquals(3, count($map->getSet('ATLANTIC_DIVISION')->getData()));

        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $map->getCounter('teams'));
        $this->assertEquals(3, $map->getCounter('teams')->getData());

        $this->assertTrue($map->getFlag('expansion_year'));

        static::$context[] = $response->getMap()->getContext();
    }

    /**
     * @depends      testAddExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\DataType\Exception
     */
    public function testRemoveExisting($riak)
    {
        $updateSetBuilder = (new Command\Builder\UpdateSet($riak))
            ->remove('Thrashers')
            ->add('Lightning');

        // build a map update command with stale context
        $command = (new Command\Builder\UpdateMap($riak))
            ->removeFlag('expansion_year')
            ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->withContext(static::$context[0])
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());

        $command = (new Command\Builder\FetchMap($riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $map = $response->getMap();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Set', $map->getSet('ATLANTIC_DIVISION'));
        $this->assertEquals(3, count($map->getSet('ATLANTIC_DIVISION')->getData()));

        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $map->getCounter('teams'));
        $this->assertEquals(3, $map->getCounter('teams')->getData());

        $this->assertTrue($map->getFlag('expansion_year'));

        static::$context[] = $response->getMap()->getContext();
    }

    /**
     * @depends      testRemoveExisting
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testAddMapExisting($riak)
    {
        $updateMapBuilder = (new Command\Builder\UpdateMap($riak))
            ->updateFlag('notifications', FALSE)
            ->updateRegister('label', 'Email Alerts');

        // build a map update command
        $command = (new Command\Builder\UpdateMap($riak))
            ->updateMap('preferences', $updateMapBuilder)
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getStatusCode());

        $command = (new Command\Builder\FetchMap($riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();
        $map = $response->getMap();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Map', $map->getMap('preferences'));
        $this->assertEquals('Email Alerts', $map->getMap('preferences')->getRegister('label'));
        $this->assertFalse($map->getMap('preferences')->getFlag('notifications'));

        static::$context[] = $response->getMap()->getContext();
    }
}
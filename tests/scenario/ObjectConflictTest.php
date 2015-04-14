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

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Scenario tests for when Kv Object changes result in a conflict
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ObjectConflictTest extends TestCase
{
    private static $key = 'conflicted';
    private static $vclock = '';

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreTwiceWithKey($riak)
    {
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());

        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_other_data')
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testStoreTwiceWithKey
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchConflicted($riak)
    {
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('300', $response->getStatusCode());
        $this->assertTrue($response->hasSiblings());
        $this->assertNotEmpty($response->getSiblings());

        static::$vclock = $response->getVclock();
    }

    /**
     * @depends      testFetchConflicted
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testResolveConflict($riak)
    {
        $command = (new Command\Builder\StoreObject($riak))
            ->withHeader('X-Riak-Vclock', static::$vclock)
            ->buildObject('some_resolved_data')
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testResolveConflict
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchResolved($riak)
    {
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals('some_resolved_data', $response->getObject()->getData());
    }
}
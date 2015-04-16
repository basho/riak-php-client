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
class BucketOperationsTest extends TestCase
{
    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStore($riak)
    {
        // build an object
        $command = (new Command\Builder\SetBucketProperties($riak))
            ->buildBucket('test')
            ->set('allow_mult', false)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetch($riak)
    {
        // build an object
        $command = (new Command\Builder\FetchBucketProperties($riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());

        $bucket = $response->getBucket();
        $this->assertNotEmpty($bucket->getProperties());
        $this->assertFalse($bucket->getProperty('allow_mult'));
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStore2($riak)
    {
        // build an object
        $command = (new Command\Builder\SetBucketProperties($riak))
            ->buildBucket('test')
            ->set('allow_mult', true)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetch2($riak)
    {
        // build an object
        $command = (new Command\Builder\FetchBucketProperties($riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());

        $bucket = $response->getBucket();
        $this->assertNotEmpty($bucket->getProperties());
        $this->assertTrue($bucket->getProperty('allow_mult'));
    }

}
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
 * Class ObjectTest
 *
 * Functional tests related to Key-Value objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ObjectTest extends TestCase
{
    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreNewWithoutKey($riak)
    {
        // build an object
        $command = (new Command\Builder\StoreObject($riak))
            ->addObject('some_data')
            ->addBucket('users')
            ->build();

        $response = $command->execute($command);

        var_dump($response);

        $this->assertEquals('201', $response->getStatusCode());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreNewWithKey($riak)
    {
        $command = (new Command\Builder\StoreObject($riak))
            ->addObject('some_data')
            ->addLocation('some_key', 'users')
            ->build();

        $response = $command->execute($command);

        $this->assertEquals('201', $response->getStatusCode());
    }

    /*    public function testFetchExisting()
        {

        }

        public function testStoreExisting()
        {

        }

        public function testDelete()
        {

        }

        public function testFetchNotFound()
        {

        }
    */
}
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
 * Class PreflistTest
 *
 * Functional tests related to Riak Preference lists
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class PreflistTest extends TestCase
{
    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetch($riak)
    {
        // build a store object comand, get the location of the newly minted object
        $location = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build()
            ->execute()
            ->getLocation();

        // build a fetch command
        $command = (new Command\Builder\FetchPreflist($riak))
            ->atLocation($location)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode(), $response->getBody());
        $this->assertJson($response->getBody());
        $this->assertNotEmpty($response->getObject()->getData()->preflist);
        $this->assertObjectHasAttribute("partition", $response->getObject()->getData()->preflist[0]);
        $this->assertObjectHasAttribute("node", $response->getObject()->getData()->preflist[0]);
        $this->assertObjectHasAttribute("primary", $response->getObject()->getData()->preflist[0]);
    }
}

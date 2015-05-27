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
 * Functional tests verifying TSL features
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SecurityFeaturesTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        shell_exec('sudo riak-admin security enable');
    }

    public static function tearDownAfterClass()
    {
        shell_exec('sudo riak-admin security disable');
    }

    public function testUnauthorized()
    {
        $nodes = [
            (new Riak\Node\Builder())
                ->atHost(static::TEST_NODE_HOST)
                ->onPort(static::TEST_NODE_SECURE_PORT)
                ->usingPasswordAuthentication('unauthorizeduser', 'hispassword')
                ->withCertificateAuthorityFile(getcwd() . '/vendor/basho/tools/test-ca/certs/cacert.pem')
                ->build()
        ];

        $riak = new Riak($nodes);

        // build an object
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build();

        $response = $command->execute();

        // expects 401 - Unauthorized
        $this->assertEquals('401', $response->getStatusCode());
    }

    public function testPasswordAuth()
    {
        $nodes = [
            (new Riak\Node\Builder())
                ->atHost(static::TEST_NODE_HOST)
                ->onPort(static::TEST_NODE_SECURE_PORT)
                ->usingPasswordAuthentication('riakpass', 'Test1234')
                ->withCertificateAuthorityFile(getcwd() . '/vendor/basho/tools/test-ca/certs/cacert.pem')
                ->build()
        ];

        $riak = new Riak($nodes);

        // build an object
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getStatusCode(), $response->getBody());
        $this->assertNotEmpty($response->getLocation());
        $this->assertInstanceOf('\Basho\Riak\Location', $response->getLocation());
    }
}
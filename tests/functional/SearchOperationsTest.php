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
 * Class CounterTest
 *
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SearchOperationsTest extends TestCase
{
    const SCHEMA = '_yz_default';
    const INDEX = 'my_index';

    protected static $default_schema = '';

    /**
     * If for some reason our index exists after the test, delete it
     */
    public static function setUpBeforeClass()
    {
        $node = [
            (new Riak\Node\Builder)
                ->withHost(static::TEST_NODE_HOST)
                ->withPort(static::TEST_NODE_PORT)
                ->build()
        ];

        $riak = new Riak($node);

        $command = (new Command\Builder\Search\FetchIndex($riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        if ($response->getStatusCode() == '200') {
            $command = (new Command\Builder\Search\DissociateIndex($riak))
                ->buildBucket('test')
                ->build();

            $command->execute();

            $command = (new Command\Builder\Search\DeleteIndex($riak))
                ->withName(static::INDEX)
                ->build();

            $command->execute();
        }
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchSchema($riak)
    {
        $command = (new Command\Builder\Search\FetchSchema($riak))
            ->withName('_yz_default')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode(), $response->getBody());
        $this->assertEquals('application/xml', $response->getContentType());
        $this->assertNotEmpty($response->getSchema());

        static::$default_schema = $response->getSchema();
    }

    /**
     * @depends      testFetchSchema
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreSchema($riak)
    {
        $command = (new Command\Builder\Search\StoreSchema($riak))
            ->withName('users')
            ->withSchemaString(static::$default_schema)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchIndexNotFound($riak)
    {
        $command = (new Command\Builder\Search\FetchIndex($riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getStatusCode(), $response->getBody());
    }

    /**
     * @depends      testFetchIndexNotFound
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreIndex($riak)
    {
        $command = (new Command\Builder\Search\StoreIndex($riak))
            ->withName(static::INDEX)
            ->usingSchema('_yz_default')
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());

        $command = (new Command\Builder\Search\FetchIndex($riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        // indexes take time to propagate between solr and Riak
        $attempts = 1;
        while ($response->getStatusCode() <> '200' || $attempts <= 5) {
            sleep(1);
            $response = $command->execute();
            $attempts++;
        }

        $this->assertEquals('200', $response->getStatusCode(), $response->getBody());
        $this->assertEquals(static::SCHEMA, $response->getIndex()->schema);
    }

    /**
     * @depends      testStoreIndex
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testAssociateIndex($riak)
    {
        $command = (new Command\Builder\Search\AssociateIndex($riak))
            ->withName(static::INDEX)
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());

        sleep(1);

        $command = (new Command\Builder\Search\DissociateIndex($riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());
    }

    /**
     * @depends      testAssociateIndex
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testDeleteIndex($riak)
    {
        $command = (new Command\Builder\Search\DeleteIndex($riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode(), $response->getBody());

        $command = (new Command\Builder\Search\FetchIndex($riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        // indexes take time to propagate between solr and Riak
        $attempts = 1;
        while ($response->getStatusCode() <> '404' || $attempts <= 5) {
            sleep(1);
            $response = $command->execute();
            $attempts++;
        }

        $this->assertEquals('404', $response->getStatusCode(), $response->getBody());
    }
}
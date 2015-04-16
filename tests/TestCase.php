<?php

/*
Copyright 2014 Basho Technologies, Inc.

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
use Basho\Riak\Node;

/**
 * Main class for testing Riak clustering
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    const TEST_NODE_HOST = 'riak-test';
    const TEST_NODE_PORT = 8098;
    const TEST_NODE_SECURE_PORT = 10011;

    const SEARCH_BUCKET_TYPE = 'phptest_search';
    const COUNTER_BUCKET_TYPE = 'phptest_counters';
    const MAP_BUCKET_TYPE = 'phptest_maps';
    const SET_BUCKET_TYPE = 'phptest_sets';
    const LEVELDB_BUCKET_TYPE = 'phptest_leveldb';

    /**
     * DATA PROVIDERS
     *
     * Prepares & provides input parameters needed by tests
     */

    /**
     * Gets a cluster of 3 fake nodes
     *
     * @return array
     */
    public function getCluster()
    {
        return [
            [
                (new Node\Builder)
                    ->onPort(static::TEST_NODE_PORT)
                    ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',])
            ],
        ];
    }

    public function getLocalNodeConnection()
    {
        $node = $this->getLocalNode();

        return [
            [
                new Riak($node[0])
            ],
        ];
    }

    /**
     * Gets a single local node
     *
     * @return array
     */
    public function getLocalNode()
    {
        return [
            [
                (new Node\Builder)
                    ->atHost(static::TEST_NODE_HOST)
                    ->onPort(static::TEST_NODE_PORT)
                    ->build()
            ],
        ];
    }
}
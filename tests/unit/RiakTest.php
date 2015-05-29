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
use Basho\Riak\Node\Builder;

/**
 * Main class for testing Riak clustering
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class RiakTest extends TestCase
{
    /**
     * @dataProvider getCluster
     *
     * @param $nodes array
     */
    public function testNodeCount($nodes)
    {
        $riak = new Riak($nodes);
        $this->assertEquals(count($riak->getNodes()), count($nodes));
    }

    /**
     * @dataProvider getCluster
     *
     * @param $nodes array
     */
    public function testConfig($nodes)
    {
        $riak = new Riak($nodes, ['max_connect_attempts' => 5]);
        $this->assertEquals(5, $riak->getConfigValue('max_connect_attempts'));
    }

    /**
     * @dataProvider getCluster
     * @param $nodes array
     */
    public function testPickNode($nodes)
    {
        $riak = new Riak($nodes);
        $this->assertNotFalse($riak->getActiveNodeIndex());
        $this->assertInstanceOf('Basho\Riak\Node', $riak->getActiveNode());
    }

    /**
     * @dataProvider getCluster
     * @param $nodes array
     */
    public function testApi($nodes)
    {
        $riak = new Riak($nodes);
        $this->assertInstanceOf('Basho\Riak\Api', $riak->getApi());
    }
}
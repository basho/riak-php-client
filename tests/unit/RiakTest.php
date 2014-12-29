<?php

/*
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

use Basho\Riak\Node;
use Basho\Riak\Node\Builder;
use Basho\Riak;

/**
 * Class RiakTest
 *
 * Main class for testing Riak clustering
 *
 * @package     Basho\Tests\RiakTest
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class RiakTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Riak Node objects.
     *
     * @var Node[]
     */
    static $nodes = null;

    /**
     * setUpBeforeClass
     *
     * Sets up the data objects needed by the tests
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        static::$nodes = (new Builder)
            ->withPort(10018)
            ->buildCluster(['riak1.company.com','riak2.company.com','riak3.company.com',]);
    }

    /**
     * testNodeCount
     */
    public function testNodeCount()
    {
        $riak = new Riak(static::$nodes);
        $this->assertEquals(count($riak->getNodes()), count(static::$nodes));
    }

    /**
     * testClientId
     */
    public function testClientId()
    {
        $riak = new Riak(static::$nodes);
        $this->assertNotEmpty($riak->getClientID());
        $this->assertRegExp('/^php_([a-z0-9])+$/', $riak->getClientID());
    }

    /**
     * testConfig
     */
    public function testConfig()
    {
        $riak = new Riak(static::$nodes, ['max_connect_attempts' => 5]);
        $this->assertEquals(5, $riak->getConfigValue('max_connect_attempts'));
    }

    /**
     * testPickNode
     */
    public function testPickNode()
    {
        $riak = new Riak(static::$nodes);
        $this->assertNotFalse($riak->getActiveNodeIndex());
        $this->assertInstanceOf('Basho\Riak\Node', $riak->getActiveNode());
    }

    /**
     * testApi
     */
    public function testApi()
    {
        $riak = new Riak(static::$nodes);
        $this->assertInstanceOf('Basho\Riak\Api', $riak->getApi());
    }
}
 
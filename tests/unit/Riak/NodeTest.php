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

namespace Basho\Tests\Riak;

use Basho\Riak\Node\Builder;

/**
 * Class NodeTest
 *
 * Main class for testing Riak clustering
 *
 * @package     Basho\Tests\RiakTest
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Riak Node object.
     *
     * @var Node
     */
    private $node = null;

    protected function setUp()
    {
        $this->markTestIncomplete();

        $this->node = (new Builder)
            ->withHost('localhost')
            ->withPort(10018)
            ->build();
    }

    /**
     * testConfig
     *
     * Test the node config object
     *
     * @covers
     */
    public function testConfig()
    {
        $this->assertEquals('localhost', $this->node->getHost());
        $this->assertEquals(10018, $this->node->getPort());
        $this->assertNotEmpty($this->node->getSignature());
    }
}

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

namespace Basho\Tests\Riak\Node;

use Basho\Riak\Node\Builder;

/**
 * Class BuilderTest
 *
 * Tests the configuration of Riak nodes via the Node Builder class
 *
 * @package     Basho\Tests\RiakTest
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testConstruct
     *
     * Test node builder construct
     *
     * @covers Builder::__construct
     */
    public function testConstruct()
    {
        $builder = new Builder();

        $this->assertInstanceOf('Basho\Riak\Node\Builder', $builder);
    }

    /**
     * testWithHost
     *
     * @covers Builder::withHost
     */
    public function testWithHost()
    {
        $builder = (new Builder)
            ->withHost('localhost');

        $this->assertEquals($builder->getConfig()->getHost(), 'localhost');
    }

    /**
     * testWithPort
     *
     * @covers Builder::withPort
     */
    public function testWithPort()
    {
        $builder = (new Builder)
            ->withPort(10018);

        $this->assertEquals($builder->getConfig()->getPort(), 10018);
    }

    /**
     * testBuildLocalhost
     *
     * Test the localhost node builder
     *
     * @covers Builder::buildLocalhost
     */
    public function testBuildLocalhost()
    {
        $nodes = (new Builder)
            ->buildLocalhost([10018, 10028, 10038, 10048, 10058]);

        $this->assertTrue(count($nodes) == 5);
        $this->assertTrue($nodes[0]->getHost() == 'localhost');
        $this->assertTrue($nodes[0]->getPort() == 10018);
    }

    /**
     * testBuildCluster
     *
     * Test the cluster node builder
     *
     * @covers Builder::buildCluster
     */
    public function testBuildCluster()
    {
        $nodes = (new Builder)
            ->withPort(10018)
            ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',]);

        $this->assertTrue(count($nodes) == 3);
        $this->assertTrue($nodes[1]->getHost() == 'riak2.company.com');
        $this->assertTrue($nodes[0]->getPort() == 10018);
        $this->assertTrue($nodes[1]->getPort() == 10018);
    }
}
 
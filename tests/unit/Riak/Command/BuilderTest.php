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

namespace Basho\Tests\Riak\Command;

use Basho\Riak\Command\Builder;
use Basho\Riak\Command\Object\Store;
use Basho\Riak\DataType\Counter;
use Basho\Riak\Object;

/**
 * Class BuilderTest
 *
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testConstruct
     *
     * Test command builder construct
     *
     * @covers Builder::__construct
     */
    public function testConstruct()
    {
        $builder = new Builder(new Store());

        $this->assertInstanceOf('Basho\Riak\Command\Builder', $builder);
        $this->assertInstanceOf('Basho\Riak\Command\Object\Store', $builder->build());
    }

    public function testWithObject()
    {
        $builder = (new Builder(new Store()))->withObject(new Object('test_key'));
        $command = $builder->build();

        $this->assertEquals('test_key', $command->getObject()->getKey());
    }

    public function testWithDataType()
    {
        $builder = (new Builder(new Store()))->withDataType(new Counter('test_key'));
        $command = $builder->build();

        $this->assertEquals('test_key', $command->getDataType()->getKey());
    }

    public function testWithParameter()
    {
        $builder = (new Builder(new Store()))->withParameter('key', TRUE);
        $command = $builder->build();

        $this->assertTrue($command->hasParameters());
        $this->assertTrue($command->getParameter('key'));
    }

    public function testWithParameters()
    {
        $builder = (new Builder(new Store()))->withParameters(['one', 'two', '3']);
        $command = $builder->build();

        $this->assertTrue($command->hasParameters());
        $this->assertCount(3, $command->getParameters());
        $this->assertNotEmpty($command->getParameter(2));
    }
}
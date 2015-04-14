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

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class QueryIndexTest extends TestCase
{
    /**
     * Test command builder construct
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testQuery($riak)
    {
        // build an object
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket', 'some_bucket_type')
                ->withIndexName('foo_int')
                ->withScalarValue(42);

        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\Indexes\Query', $command);
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('some_bucket_type', $command->getBucket()->getType());
        $this->assertEquals('foo_int', $command->getIndexName());
        $this->assertEquals('42', $command->getMatchValue());
    }

    /**
     * Tests validate properly verifies the index name is not there
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateLocation($riak)
    {
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket');

        $builder->build();
    }

    /**
     * Tests validate properly verifies the scalar match value is not there
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateScalarValue($riak)
    {
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket')
                ->withIndexName("foo_int")
                ->withScalarValue(null);

        $builder->build();
    }

    /**
     * Tests validate properly verifies the range lower bound value is not there
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateRangeLowerBound($riak)
    {
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket')
            ->withIndexName("foo_int")
            ->withRangeValue(null, 42);

        $builder->build();
    }

    /**
     * Tests validate properly verifies the range upper bound value is not there
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateRangeUpperBound($riak)
    {
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket')
            ->withIndexName("foo_int")
            ->withRangeValue(42, null);

        $builder->build();
    }

    /**
     * Test command builder defaults for options
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testOptionDefaults($riak)
    {
        // build an object
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket', 'some_bucket_type')
            ->withIndexName('foo_int')
            ->withScalarValue(42);

        $command = $builder->build();

        $parameters = $command->getParameters();

        $this->assertFalse(isset($parameters['continuation']));
        $this->assertFalse(isset($parameters['return_terms']));
        $this->assertFalse(isset($parameters['pagination_sort']));
        $this->assertFalse(isset($parameters['term_regex']));
        $this->assertFalse(isset($parameters['timeout']));
    }

    /**
     * Test command builder settings for options
     *
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testOptionSettings($riak)
    {
        // build an object
        $builder = new Command\Builder\QueryIndex($riak);
        $builder->buildBucket('some_bucket', 'some_bucket_type')
            ->withIndexName('foo_int')
            ->withScalarValue(42)
            ->withContinuation('12345')
            ->withReturnTerms(true)
            ->withPaginationSort(true)
            ->withTermFilter('foobar')
            ->withTimeout(43);

        $command = $builder->build();



        $this->assertEquals('12345', $command->getParameter('continuation'));
        $this->assertEquals('true', $command->getParameter('return_terms'));
        $this->assertEquals('true', $command->getParameter('pagination_sort'));
        $this->assertEquals('foobar', $command->getParameter('term_regex'));
        $this->assertEquals(43, $command->getParameter('timeout'));
    }
}
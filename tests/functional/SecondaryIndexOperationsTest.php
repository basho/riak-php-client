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
use Basho\Riak\Object;

/**
 * Functional tests related to secondary indexes
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class SecondaryIndexOperationsTest extends TestCase
{
    private static $key = '';
    private static $bucket = '';

    /**
     * @var \Basho\Riak\Object|null
     */
    private static $object = NULL;

    /**
     * @var array|null
     */
    private static $vclock = NULL;

    public static function setUpBeforeClass()
    {
        // make completely random key/bucket based on time
        static::$key = md5(rand(0, 99) . time());
        static::$bucket = md5(rand(0, 99) . time());
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testStoreObjectWithIndexes($riak)
    {
        $object = new Object('person');
        $object->addValueToIndex('lucky_numbers_int', 42);
        $object->addValueToIndex('lucky_numbers_int', 64);
        $object->addValueToIndex('lastname_bin', 'Knuth');

        $command = (new Command\Builder\StoreObject($riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());
    }

    /**
     * @depends      testStoreObjectWithIndexes
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testFetchObjectWithIndexes($riak)
    {
        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals('person', $response->getObject()->getData());
        $this->assertNotEmpty($response->getVClock());
        $indexes = $response->getObject()->getIndexes();
        $this->assertEquals($indexes['lucky_numbers_int'], [42, 64]);
        $this->assertEquals($indexes['lastname_bin'], ['Knuth']);

        static::$object = $response->getObject();
        static::$vclock = $response->getVclock();
    }

    /**
     * @depends      testFetchObjectWithIndexes
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testRemoveIndexes($riak)
    {
        $object = static::$object;
        $object->removeValueFromIndex('lucky_numbers_int', 64);
        $object->removeValueFromIndex('lastname_bin', 'Knuth');

        $command = (new Command\Builder\StoreObject($riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->withHeader("X-Riak-Vclock", static::$vclock)
            ->build();

        // TODO: internalize Vclock to Riak\Object.

        $response = $command->execute();

        $this->assertEquals('204', $response->getStatusCode());

        $command = (new Command\Builder\FetchObject($riak))
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals('person', $response->getObject()->getData());
        $indexes = $response->getObject()->getIndexes();
        $this->assertEquals($indexes['lucky_numbers_int'], [42]);
    }

    /**
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testSetupIndexObjects($riak)
    {
        for($x = 0; $x <= 1000; $x++) {
            $object = (new Object('student'.$x))
                        ->addValueToIndex('lucky_numbers_int', $x) // 0,1,2...
                        ->addValueToIndex('group_int', $x % 2)     // 0,0,1,1,2,2,3,3,...
                        ->addValueToIndex('grade_bin', chr(65 + ($x % 6))) // A,B,C,D,E,F,A...
                        ->addValueToIndex('lessThan500_bin', $x < 500 ? 'less' : 'more');

            $command = (new Command\Builder\StoreObject($riak))
                ->withObject($object)
                ->buildLocation('student'.$x, 'Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
                ->build();

            $response = $command->execute();
            $this->assertEquals('204', $response->getStatusCode());
        }
    }

    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testScalarQuery($riak)
    {
        $command = (new Command\Builder\QueryIndex($riak))
                        ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
                        ->withIndexName('lucky_numbers_int')
                        ->withScalarValue(5)
                        ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(1, count($response->getResults()));
        $this->assertEquals('student5', $response->getResults()[0]);
    }

    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testRangeQuery($riak)
    {
        $command = (new Command\Builder\QueryIndex($riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('grade_bin')
            ->withRangeValue('A', 'B')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $matches = $response->getResults();
        sort($matches, SORT_NATURAL | SORT_FLAG_CASE);
        $this->assertEquals(334, count($matches));
        $this->assertEquals(['student0','student1','student6','student7'], array_slice($matches, 0, 4));
    }

    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testReturnTerms($riak)
    {
        $keysAndTerms = [['A' => 'student0'], ['B' => 'student1'], ['A' => 'student6'], ['B' => 'student7']];

        $command = (new Command\Builder\QueryIndex($riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('grade_bin')
            ->withRangeValue('A', 'B')
            ->withReturnTerms(true)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $matches = $response->getResults();
        usort($matches, function($a, $b) { return strnatcmp(current($a), current($b)); });

        $this->assertEquals(334, count($matches));
        $this->assertTrue($response->hasReturnTerms());
        $this->assertEquals($keysAndTerms, array_slice($matches, 0, 4));
    }



    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testGettingKeysWithContinuationWorks($riak)
    {
        $builder = (new Command\Builder\QueryIndex($riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lucky_numbers_int')
            ->withRangeValue(0,3)
            ->withMaxResults(3);

        // Get first page
        $command = $builder->build();
        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(3, count($response->getResults()));
        $this->assertNotNull($response->getContinuation());
        $this->assertFalse($response->isDone());

        // Get second page
        $builder = $builder->withContinuation($response->getContinuation());
        $command2 = $builder->build();

        $response2 = $command2->execute($command2);

        $this->assertEquals('200', $response2->getStatusCode());
        $this->assertEquals(1, count($response2->getResults()));
        $this->assertNull($response2->getContinuation());
        $this->assertTrue($response2->isDone());
    }

    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testTimeoutWorks($riak)
    {
        $builder = (new Command\Builder\QueryIndex($riak))
            ->buildBucket('Students' . static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lucky_numbers_int')
            ->withRangeValue(0, 1000)
            ->withTimeout(1);

        // Get first page
        $command = $builder->build();
        $response = $command->execute();
        $this->assertFalse($response->isSuccess());
        $this->assertContains($response->getStatusCode(), ['500', '503']);
        $this->assertEquals(0, count($response->getResults()));
        $this->assertNull($response->getContinuation());
        $this->assertTrue($response->isDone());
    }

    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testUsingPaginationSortWillSortResultsWhilePaging($riak)
    {
        $builder = (new Command\Builder\QueryIndex($riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lucky_numbers_int')
            ->withRangeValue(0,500)
            ->withMaxResults(10)
            ->withReturnTerms(true);

        // Get first page
        $command = $builder->build();
        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(['0' => 'student0'], $response->getResults()[0]);

        // Get second page
        $builder = $builder->withContinuation($response->getContinuation());
        $command2 = $builder->build();

        $response2 = $command2->execute($command2);

        $this->assertEquals('200', $response2->getStatusCode());
        $this->assertEquals(10, count($response2->getResults()));
        $this->assertEquals(['10' => 'student10'], $response2->getResults()[0]);
    }


    /**
     * @depends      testSetupIndexObjects
     * @dataProvider getLocalNodeConnection
     *
     * @param $riak \Basho\Riak
     */
    public function testUsingTermRegexOnARangeFiltersTheResults($riak)
    {
        $builder = (new Command\Builder\QueryIndex($riak))
            ->buildBucket('Students' . static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lessThan500_bin')
            ->withRangeValue('a', 'z')
            ->withTermFilter('^less');

        // Get first page
        $command = $builder->build();
        $response = $command->execute();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(500, count($response->getResults()));
    }
}
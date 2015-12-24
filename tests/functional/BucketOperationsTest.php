<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Class CounterTest
 *
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class BucketOperationsTest extends TestCase
{
    public function testStore()
    {
        // build an object
        $command = (new Command\Builder\SetBucketProperties(static::$riak))
            ->buildBucket('test')
            ->set('allow_mult', false)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetch()
    {
        // build an object
        $command = (new Command\Builder\FetchBucketProperties(static::$riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());

        $bucket = $response->getBucket();
        $this->assertNotEmpty($bucket->getProperties());
        $this->assertFalse($bucket->getProperty('allow_mult'));
    }

    public function testStore2()
    {
        // build an object
        $command = (new Command\Builder\SetBucketProperties(static::$riak))
            ->buildBucket('test')
            ->set('allow_mult', true)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetch2()
    {
        // build an object
        $command = (new Command\Builder\FetchBucketProperties(static::$riak))
            ->buildBucket('test')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());

        $bucket = $response->getBucket();
        $this->assertNotEmpty($bucket->getProperties());
        $this->assertTrue($bucket->getProperty('allow_mult'));
    }

}

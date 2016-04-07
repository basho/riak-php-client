<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Functional tests related to TimeSeries operations
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class TimeSeriesOperationsTest extends TestCase
{
    use TimeSeriesTrait;

    const TABLE_DEFINITION = "
        CREATE TABLE %s (
            geohash varchar not null,
            user varchar not null,
            time timestamp not null,
            weather varchar not null,
            temperature double,
            uv_index sint64,
            observed boolean not null,
            PRIMARY KEY((geohash, user, quantum(time, 15, 'm')), geohash, user, time)
        )";

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::populateKey();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    public function testFetchTableDescriptionNotFound()
    {
        $command = (new Command\Builder\TimeSeries\DescribeTable(static::$riak))
            ->withTable(static::$table . 'notfound')
            ->build();

        $this->assertInstanceOf('Basho\Riak\Command\TimeSeries\Query\Fetch', $command);

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode(), $response->getMessage());
    }

    public function testCreateTable()
    {
        $command = (new Command\Builder\TimeSeries\Query(static::$riak))
            ->withQuery(sprintf(static::TABLE_DEFINITION, static::$table . rand(0,1000)))
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetchTableDescription()
    {
        $command = (new Command\Builder\TimeSeries\DescribeTable(static::$riak))
            ->withTable(static::$table)
            ->build();

        $this->assertInstanceOf('Basho\Riak\Command\TimeSeries\Query\Fetch', $command);

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertNotEmpty($response->getResults());
        $this->assertCount(7, $response->getResults());
        $this->assertCount(5, $response->getResult());
    }

    public function testFetchRowNotFound()
    {
        $response = (new Command\Builder\TimeSeries\FetchRow(static::$riak))
            ->atKey(static::$key)
            ->inTable(static::$table)
            ->build()
            ->execute();

        $this->assertEquals('404', $response->getCode(), $response->getMessage());
    }

    public function testStoreRow()
    {
        $response = (new Command\Builder\TimeSeries\StoreRows(static::$riak))
            ->inTable(static::$table)
            ->withRow(static::generateRow())
            ->build()
            ->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetchRow()
    {
        /** @var Command\TimeSeries\Response $response */
        $response = (new Command\Builder\TimeSeries\FetchRow(static::$riak))
            ->atKey(static::$key)
            ->inTable(static::$table)
            ->build()
            ->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertCount(7, $response->getRow());

        $this->assertEquals("geohash", $response->getRow()[0]->getName());
        $this->assertEquals("hash1", $response->getRow()[0]->getValue());
        $this->assertEquals("varchar", $response->getRow()[0]->getType());

        $this->assertEquals("uv_index", $response->getRow()[5]->getName());
        $this->assertEquals(10, $response->getRow()[5]->getValue());
        $this->assertEquals("sint64", $response->getRow()[5]->getType());
    }

    public function testStoreRows()
    {
        $response = (new Command\Builder\TimeSeries\StoreRows(static::$riak))
            ->inTable(static::$table)
            ->withRows(static::generateRows())
            ->build()
            ->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testDeleteRow()
    {
        $response = (new Command\Builder\TimeSeries\DeleteRow(static::$riak))
            ->atKey(static::$key)
            ->inTable(static::$table)
            ->build()
            ->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testQueryNotFound()
    {
        $response = (new Command\Builder\TimeSeries\Query(static::$riak))
            ->withQuery("select * from GeoCheckin where time > 0 and time < 10 and geohash = 'hash1' and user = 'user1'")
            ->build()
            ->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
        $this->assertCount(0, $response->getResults());
    }

    public function testQuery()
    {
        $upper_bound = static::$now->getTimestamp() + 1;
        $lower_bound = static::oneHourAgo() - 1;

        $response = (new Command\Builder\TimeSeries\Query(static::$riak))
            ->withQuery("select * from GeoCheckin where geohash = 'hash1' and user = 'user1' and (time > {$lower_bound} and time < {$upper_bound})")
            ->build()
            ->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertCount(0, $response->getResults());
    }
}

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

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::populateKey();

        try {
            $response = (new Command\Builder\TimeSeries\DescribeTable(static::$riak))
                ->withTable(static::$table)
                ->build()
                ->execute();
        } catch (\Basho\Riak\Exception $e) {
            $command = (new Command\Builder\TimeSeries\Query(static::$riak))
                ->withQuery(static::tableDefinition())
                ->build()
                ->execute();
        }
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
            ->withQuery(static::tableDefinition(static::$table . rand(0,1000)))
            ->build();

        $response = $command->execute();

        $this->assertContains($response->getCode(), ['200','204'], $response->getMessage());
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
            ->inTable(static::$table . 'notfound')
            ->build()
            ->execute();

        $this->assertEquals('404', $response->getCode(), $response->getMessage());
    }

    public function testQueryNotFound()
    {
        $response = (new Command\Builder\TimeSeries\Query(static::$riak))
            ->withQuery("select * from " . self::$table . " where time > 0 and time < 10 and region = 'South Atlantic' and state = 'South Carolina'")
            ->build()
            ->execute();

        $this->assertContains($response->getCode(), ['200','204'], $response->getMessage());
        $this->assertCount(0, $response->getResults());
    }

    public function testStoreRow()
    {
        $response = (new Command\Builder\TimeSeries\StoreRows(static::$riak))
            ->inTable(static::$table)
            ->withRow(static::generateRow())
            ->build()
            ->execute();

        $this->assertContains($response->getCode(), ['200','204'], $response->getMessage());
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

        if (static::$riak->getApi() instanceof Riak\Api\Pb) {
            $this->assertEquals("region", $response->getRow()[0]->getName());
            $this->assertEquals("South Atlantic", $response->getRow()[0]->getValue());
            $this->assertEquals("varchar", $response->getRow()[0]->getType());

            $this->assertEquals("uv_index", $response->getRow()[5]->getName());
            $this->assertEquals(10, $response->getRow()[5]->getValue());
            $this->assertEquals("sint64", $response->getRow()[5]->getType());
        } else {
            $this->assertEquals("South Atlantic", $response->getRow()['region']);
            $this->assertEquals(10, $response->getRow()['uv_index']);
        }
    }

    public function testStoreRows()
    {
        $response = (new Command\Builder\TimeSeries\StoreRows(static::$riak))
            ->inTable(static::$table)
            ->withRows(static::generateRows())
            ->build()
            ->execute();

        $this->assertContains($response->getCode(), ['200','204'], $response->getMessage());
    }

    /**
     * @depends      testStoreRows
     */
    public function testQuery()
    {
        $upper_bound = static::$now->getTimestamp() + 1;
        $lower_bound = static::oneHourAgo() - 1;

        $response = (new Command\Builder\TimeSeries\Query(static::$riak))
            ->withQuery("select * from "  . static::$table . " where region = 'South Atlantic' and state = 'South Carolina' and (time > {$lower_bound} and time < {$upper_bound})")
            ->build()
            ->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertCount(2, $response->getResults());
        $this->assertCount(7, $response->getResult());
        if (static::$riak->getApi() instanceof Riak\Api\Pb) {
            $this->assertEquals('South Atlantic', $response->getResults()[0][0]->getValue());
        } else {
            $this->assertEquals('South Atlantic', $response->getResult()['region']);
        }
    }

    /**
     * @depends      testStoreRow
     */
    public function testDeleteRow()
    {
        $response = (new Command\Builder\TimeSeries\DeleteRow(static::$riak))
            ->atKey(static::$key)
            ->inTable(static::$table)
            ->build()
            ->execute();

        $this->assertContains($response->getCode(), ['200','204'], $response->getMessage());
    }
}

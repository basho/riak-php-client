<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Riak\TimeSeries\Cell;
use Basho\Tests\TestCase;
use Basho\Tests\TimeSeriesTrait;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchRowTest extends TestCase
{
    use TimeSeriesTrait;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::populateKey();
    }

    /**
     * Test command builder construct
     */
    public function testFetch()
    {
        // initialize builder
        $builder = (new Command\Builder\TimeSeries\FetchRow(static::$riak))
            ->atKey(static::$key)
            ->inTable(static::$table);

        // build a command
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\TimeSeries\Fetch', $command);
        $this->assertEquals(static::$table, $command->getTable());
        $this->assertEquals(static::$key, $command->getData());
        $this->assertEquals("geohash/hash1/user/user1/time/1234567", implode("/", $command->getData()));

        // change the key and reuse builder to create new command
        $key = static::$key;
        $key[2]->setTimestampValue(1234568);
        $command = $builder->atKey($key)->build();

        $this->assertEquals("geohash/hash1/user/user1/time/1234568", implode("/", $command->getData()));
    }
}

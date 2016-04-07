<?php

namespace Basho\Tests;

use Basho\Riak\TimeSeries\Cell;

/**
 * Helps with reusability for timeseries commands
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */

trait TimeSeriesTrait
{
    protected static $table = "GeoCheckin";
    protected static $key = [];
    protected static $now;

    protected static function populateKey()
    {
        static::$now = new \DateTime("@1443806900");

        static::$key = [
            (new Cell("geohash"))->setValue("hash1"),
            (new Cell("user"))->setValue("user1"),
            (new Cell("time"))->setTimestampValue(static::$now->getTimestamp()),
        ];
    }

    public static function generateRows()
    {
        $row = static::generateRow();
        $rows = [
            $row,
            [
                $row[0],
                $row[1],
                (new Cell("time"))->setTimestampValue(static::oneHourAgo()),
                (new Cell("weather"))->setValue("windy"),
                (new Cell("temperature"))->setDoubleValue(19.8),
                (new Cell("uv_index"))->setIntValue(10),
                (new Cell("observed"))->setBooleanValue(true),
            ],
            [
                $row[0],
                $row[1],
                (new Cell("time"))->setTimestampValue(static::twoHoursAgo()),
                (new Cell("weather"))->setValue("cloudy"),
                (new Cell("temperature"))->setDoubleValue(19.1),
                (new Cell("uv_index"))->setIntValue(15),
                (new Cell("observed"))->setBooleanValue(false),
            ],
        ];

        return $rows;
    }

    public static function generateRow()
    {
        $row = static::$key;
        $row[] = (new Cell("weather"))->setValue("hot");
        $row[] = (new Cell("temperature"))->setDoubleValue(23.5);
        $row[] = (new Cell("uv_index"))->setIntValue(10);
        $row[] = (new Cell("observed"))->setBooleanValue(true);

        return $row;
    }

    public static function oneHourAgo()
    {
        return static::$now->getTimestamp() - 60 * 60 * 1;
    }

    public static function twoHoursAgo()
    {
        return static::$now->getTimestamp() - 60 * 60 * 2;
    }
}

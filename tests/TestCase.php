<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Node;

/**
 * Main class for testing Riak clustering
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    const TEST_NODE_HOST = 'riak-test';
    const TEST_NODE_PORT = 8087;
    const TEST_NODE_HTTP_PORT = 8098;
    const TEST_NODE_SECURE_PORT = 10011;

    const SEARCH_BUCKET_TYPE = 'phptest_search';
    const COUNTER_BUCKET_TYPE = 'phptest_counters';
    const MAP_BUCKET_TYPE = 'phptest_maps';
    const SET_BUCKET_TYPE = 'phptest_sets';
    const LEVELDB_BUCKET_TYPE = 'phptest_leveldb';

    /**
     * @var \Basho\Riak|null
     */
    static $riak = null;

    /**
     * Gets a cluster of 3 fake nodes
     *
     * @return array
     */
    public static function getCluster()
    {
        return (new Node\Builder)
            ->onPort(static::getTestPort())
            ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',]);
    }

    public static function getLocalNode()
    {
        return (new Node\Builder)
            ->atHost(static::getTestHost())
            ->onPort(static::getTestPort())
            ->build();
    }

    public static function getApiBridgeClass()
    {
        return !empty($_ENV['PB_INTERFACE']) ? new Riak\Api\Pb() : null;
    }

    public static function getTestHost()
    {
        $host = getenv('RIAK_HOST');
        return $host ?: static::TEST_NODE_HOST;
    }

    public static function getTestPort()
    {
        if (getenv('PB_INTERFACE')) {
            $port = getenv('RIAK_PORT') ? getenv('RIAK_PORT') : static::TEST_NODE_PORT;
        } else {
            $port = getenv('RIAK_HTTP_PORT') ? getenv('RIAK_HTTP_PORT') : static::TEST_NODE_HTTP_PORT;
        }

        return $port;
    }

    public static function getTestSecurePort()
    {
        if (getenv('PB_INTERFACE')) {
            $port = static::getTestPort();
        } else {
            $port = getenv('RIAK_HTTPS_PORT') ? getenv('RIAK_HTTPS_PORT') : static::TEST_NODE_SECURE_PORT;
        }

        return $port;
    }

    /**
     * Parent setup method opens Riak connection and initializes static variable
     */
    public static function setUpBeforeClass()
    {
        static::$riak = new Riak([static::getLocalNode()], [], static::getApiBridgeClass());
    }

    /**
     * Parent tear down method closes Riak connection and uninitializes static variable
     */
    public static function tearDownAfterClass()
    {
        static::$riak->getApi()->closeConnection();
        static::$riak = null;
    }
}

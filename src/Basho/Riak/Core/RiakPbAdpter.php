<?php

namespace Basho\Riak\Core;

use React\SocketClient\ConnectorInterface;
use Basho\Riak\Core\Message\Request;
use Basho\Riak\RiakException;

/**
 * Proto buf adapter for riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakPbAdpter implements RiakAdapter
{
    private $strategyMap = [
        // kv
        'Basho\Riak\Core\Message\Kv\GetRequest'       => 'Basho\Riak\Core\Adapter\Rpb\Kv\HttpGet',
        'Basho\Riak\Core\Message\Kv\PutRequest'       => 'Basho\Riak\Core\Adapter\Rpb\Kv\HttpPut',
        'Basho\Riak\Core\Message\Kv\DeleteRequest'    => 'Basho\Riak\Core\Adapter\Rpb\Kv\HttpDelete',
        // crdt
        'Basho\Riak\Core\Message\DataType\GetRequest' => 'Basho\Riak\Core\Adapter\Rpb\DataType\HttpGet',
        'Basho\Riak\Core\Message\DataType\PutRequest' => 'Basho\Riak\Core\Adapter\Rpb\DataType\HttpPut',
        // bucket
        'Basho\Riak\Core\Message\Bucket\GetRequest'   => 'Basho\Riak\Core\Adapter\Rpb\Bucket\HttpGet',
        'Basho\Riak\Core\Message\Bucket\PutRequest'   => 'Basho\Riak\Core\Adapter\Rpb\Bucket\HttpPut',
    ];

    /**
     * @var \React\SocketClient\ConnectorInterface
     */
    private $connector;

    /**
     * @param \React\SocketClient\ConnectorInterface $connector
     */
    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @return \React\SocketClient\ConnectorInterface
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @param \Basho\Riak\Core\Message\Request $request
     *
     * @return \Basho\Riak\Core\Adapter\Strategy
     */
    private function createAdapterStrategyFor(Request $request)
    {
        $requestClass  = get_class($request);
        $strategyClass = isset($this->strategyMap[$requestClass])
            ? $this->strategyMap[$requestClass]
            : null;

        if ($strategyClass !== null) {
            return new $strategyClass($this->connector);
        }

        throw new RiakException(sprintf("Unknown message : %s", get_class($request)));
    }

    /**
     * {@inheritdoc}
     */
    public function send(Request $request)
    {
        return $this->createAdapterStrategyFor($request)->send($request);
    }
}

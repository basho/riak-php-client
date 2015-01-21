<?php

namespace Basho\Riak\Core;

use InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Message\Request;

/**
 * Http adapter for riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakHttpAdpter implements RiakAdapter
{
    private $strategyMap = [
        // kv
        'Basho\Riak\Core\Message\Kv\GetRequest'       => 'Basho\Riak\Core\Adapter\Http\Kv\HttpGet',
        'Basho\Riak\Core\Message\Kv\PutRequest'       => 'Basho\Riak\Core\Adapter\Http\Kv\HttpPut',
        'Basho\Riak\Core\Message\Kv\DeleteRequest'    => 'Basho\Riak\Core\Adapter\Http\Kv\HttpDelete',
        // crdt
        'Basho\Riak\Core\Message\DataType\GetRequest' => 'Basho\Riak\Core\Adapter\Http\DataType\HttpGet',
        'Basho\Riak\Core\Message\DataType\PutRequest' => 'Basho\Riak\Core\Adapter\Http\DataType\HttpPut',
        // bucket
        'Basho\Riak\Core\Message\Bucket\GetRequest'   => 'Basho\Riak\Core\Adapter\Http\Bucket\HttpGet',
        'Basho\Riak\Core\Message\Bucket\PutRequest'   => 'Basho\Riak\Core\Adapter\Http\Bucket\HttpPut',
    ];

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
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
            return new $strategyClass($this->client);
        }

        throw new InvalidArgumentException(sprintf("Unknown message : %s", get_class($request)));
    }

    /**
     * {@inheritdoc}
     */
    public function send(Request $request)
    {
        return $this->createAdapterStrategyFor($request)->send($request);
    }
}

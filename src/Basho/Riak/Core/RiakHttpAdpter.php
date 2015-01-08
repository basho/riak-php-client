<?php

namespace Basho\Riak\Core;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\GetRequest;
use Basho\Riak\Core\Message\PutRequest;
use Basho\Riak\Core\Message\DeleteRequest;
use Basho\Riak\Core\Adapter\Kv\HttpGet;
use Basho\Riak\Core\Adapter\Kv\HttpPut;
use Basho\Riak\Core\Adapter\Kv\HttpDelete;
use Basho\Riak\RiakException;

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
        if ($request instanceof GetRequest) {
            return new HttpGet($this->client);
        }

        if ($request instanceof PutRequest) {
            return new HttpPut($this->client);
        }

        if ($request instanceof DeleteRequest) {
            return new HttpDelete($this->client);
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

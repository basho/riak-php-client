<?php

namespace Basho\Riak\Core;

use GuzzleHttp\Client;
use Basho\Riak\RiakException;
use Basho\Riak\Core\Adapter\Proto\ProtoClient;

/**
 * Riak Node builder.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakNodeBuilder
{
    /**
     * @var string
     */
    private $protocol = 'http';

    /**
     * @var string
     */
    private $host = 'localhost';

    /**
     * @var string
     */
    private $port = 8098;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @param string $protocol
     *
     * @return \Basho\Riak\Core\RiakNodeBuilder
     */
    public function withProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return \Basho\Riak\Core\RiakNodeBuilder
     */
    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param string $port
     *
     * @return \Basho\Riak\Core\RiakNodeBuilder
     */
    public function withPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $user
     *
     * @return \Basho\Riak\Core\RiakNodeBuilder
     */
    public function withUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $pass
     *
     * @return \Basho\Riak\Core\RiakNodeBuilder
     */
    public function withPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * @return \Basho\Riak\Core\RiakHttpAdpter
     */
    private function buildHttpAdapter()
    {
        $auth     = $this->user ? [$this->user, $this->pass] : null;
        $baseUrl  = "{$this->protocol}://{$this->host}:{$this->port}";
        $defaults = $auth ? [ 'auth'  => $auth ] : null;
        $client   = new Client([
            'base_url'  => $baseUrl,
            'defaults'  => $defaults,
        ]);

        return new RiakHttpAdpter($client);
    }

    /**
     * @return \Basho\Riak\Core\RiakPbAdpter
     */
    private function buildProtoAdapter()
    {
        $rpbClient    = new ProtoClient($this->host, $this->port, $this->user, $this->pass);
        $riakPbAdpter = new RiakProtoAdpter($rpbClient);

        return $riakPbAdpter;
    }

    /**
     * @return \Basho\Riak\Core\RiakAdapter
     */
    private function buildAdapter()
    {
        if ($this->protocol == 'http' || $this->protocol == 'https') {
            return $this->buildHttpAdapter();
        }

        if ($this->protocol == 'proto') {
            return $this->buildProtoAdapter();
        }

        throw new RiakException("Unknown protocol : {$this->protocol}");
    }

    /**
     * @return \Basho\Riak\Core\RiakNode
     */
    public function build()
    {
        return new RiakNode($this->buildAdapter());
    }
}

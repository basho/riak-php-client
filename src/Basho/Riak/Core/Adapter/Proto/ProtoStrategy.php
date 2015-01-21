<?php

namespace Basho\Riak\Core\Adapter\Proto;

use Basho\Riak\Core\Adapter\Strategy;
use Basho\Riak\Core\Adapter\Proto\ProtoClient;

/**
 * Base rpb strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class ProtoStrategy implements Strategy
{
    /**
     * @var \Basho\Riak\Core\Adapter\Proto\ProtoClient
     */
    protected $client;

    /**
     * @param \Basho\Riak\Core\Adapter\Proto\ProtoClient $client
     */
    public function __construct(ProtoClient $client)
    {
        $this->client = $client;
    }
}

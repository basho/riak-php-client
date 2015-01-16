<?php

namespace Basho\Riak\Core\Adapter\Rpb\Bucket;

use Basho\Riak\Core\Adapter\Strategy;
use Basho\Riak\Core\Adapter\Rpb\RpbClient;

/**
 * Base rpb strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseRpbStrategy implements Strategy
{
    /**
     * @var \Basho\Riak\Core\Adapter\Rpb\RpbClient
     */
    protected $client;

    /**
     * @param \Basho\Riak\Core\Adapter\Rpb\RpbClient $client
     */
    public function __construct(RpbClient $client)
    {
        $this->client = $client;
    }
}

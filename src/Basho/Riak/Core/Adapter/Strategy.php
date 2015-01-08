<?php

namespace Basho\Riak\Core\Adapter;

use Basho\Riak\Core\Message\Request;

/**
 * Riak adapter strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Strategy
{
    /**
     * @param \Basho\Riak\Core\Message\Request $request
     *
     * @return \Basho\Riak\Core\Message\Response
     */
    public function send(Request $request);
}

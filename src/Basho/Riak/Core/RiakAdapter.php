<?php

namespace Basho\Riak\Core;

use Basho\Riak\Core\Message\Request;

/**
 * Riak Client Adpter.
 *
 * @todo split this by each action
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakAdapter
{
    /**
     * @param \Basho\Riak\Core\Message\Request $request
     *
     * @return \Basho\Riak\Core\Message\Response
     */
    public function send(Request $request);
}

<?php

namespace Basho\Riak\Core;

use Basho\Riak\Core\Message\Request;

/**
 * Riak Client Adpter.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

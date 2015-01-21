<?php

namespace Basho\Riak\Core\Message\Bucket;

use Basho\Riak\Core\Message\Request;

/**
 * This class represents a get request.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class GetRequest extends Request
{
    public $bucket;
    public $type;
}

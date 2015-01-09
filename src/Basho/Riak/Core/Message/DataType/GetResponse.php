<?php

namespace Basho\Riak\Core\Message\DataType;

use Basho\Riak\Core\Message\Response;

/**
 * This class represents a get response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class GetResponse extends Response
{
    public $type;
    public $value;
}

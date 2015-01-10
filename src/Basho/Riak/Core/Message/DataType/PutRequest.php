<?php

namespace Basho\Riak\Core\Message\DataType;

use Basho\Riak\Core\Message\Request;

/**
 * This class represents a put request.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class PutRequest extends Request
{
    public $context;
    public $bucket;
    public $type;
    public $key;
    public $op;
    public $w;
    public $dw;
    public $pw;
    public $nVal;
    public $includeContext;
    public $sloppyQuorum;
    public $returnBody;
    public $timeout;
}

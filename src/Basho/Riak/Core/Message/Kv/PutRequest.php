<?php

namespace Basho\Riak\Core\Message\Kv;

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
    public $bucket;
    public $type;
    public $nVal;
    public $key;
    public $vClock;
    public $content;
    public $w;
    public $dw;
    public $returnBody;
    public $pw;
    public $ifNotModified;
    public $ifNoneMatch;
    public $returnHead;
}

<?php

namespace Basho\Riak\Core\Message;

/**
 * This class represents a put request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
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

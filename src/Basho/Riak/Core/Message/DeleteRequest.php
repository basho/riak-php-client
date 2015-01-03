<?php

namespace Basho\Riak\Core\Message;

/**
 * This class represents a delete request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteRequest extends Request
{
    public $vClock;
    public $bucket;
    public $type;
    public $key;
    public $r;
    public $pr;
    public $rw;
    public $w;
    public $dw;
    public $pw;
    public $basicQuorum;
    public $notfoundOk;
    public $ifModified;
    public $head;
    public $deletedvclock;
    public $timeout;
    public $sloppyQuorum;
    public $nVal;
}

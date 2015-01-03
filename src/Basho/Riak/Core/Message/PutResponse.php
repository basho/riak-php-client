<?php

namespace Basho\Riak\Core\Message;

/**
 * This class represents a put response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PutResponse extends Response
{
    public $key;
    public $vClock;
    public $contentList = [];
}

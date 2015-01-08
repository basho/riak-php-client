<?php

namespace Basho\Riak\Core\Message;

/**
 * This class represents a get response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class GetResponse extends Response
{
    public $vClock;
    public $unchanged;
    public $contentList = [];
}

<?php

namespace Basho\Riak\Core\Message;

/**
 * Base class for all responses.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Response
{
    public $vClock;
    public $contentList = [];
}

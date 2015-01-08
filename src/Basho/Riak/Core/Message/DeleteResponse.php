<?php

namespace Basho\Riak\Core\Message;

/**
 * This class represents a delete response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteResponse extends Response
{
    public $vClock;
    public $contentList = [];
}

<?php

namespace BashoRiakTest\Core\Message;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Message\Kv\GetRequest;

class MessageTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown property 'UNKNOWN_PROPERTY' on 'Basho\Riak\Core\Message\Kv\GetRequest'
     */
    public function testUnknownPropertyException()
    {
        $request = new GetRequest;

        $request->UNKNOWN_PROPERTY = 'invalid';
    }
}
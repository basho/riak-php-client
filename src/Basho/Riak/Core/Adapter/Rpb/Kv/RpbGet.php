<?php

namespace Basho\Riak\Core\Adapter\Rpb\Kv;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Kv\GetRequest;
use Basho\Riak\Core\Message\Kv\GetResponse;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\ProtoBuf\RpbGetReq;
use Basho\Riak\ProtoBuf\RpbGetResp;

/**
 * rpb get implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RpbGet extends BaseRpbStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\Kv\GetRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\RpbGetReq
     */
    private function createRpbMessage(GetRequest $request)
    {
        $message = new RpbGetReq();

        $message->setBucket($request->bucket);
        $message->setType($request->type);
        $message->setKey($request->key);

        if ($request->r !== null) {
            $message->setR($request->r);
        }

        if ($request->pr !== null) {
            $message->setPr($request->pr);
        }

        if ($request->basicQuorum !== null) {
            $message->setBasicQuorum($request->basicQuorum);
        }

        if ($request->notfoundOk !== null) {
            $message->setNotfoundOk($request->notfoundOk);
        }

        return $message;
    }

    /**
     * @param \Basho\Riak\Core\Message\Kv\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\Kv\GetResponse
     */
    public function send(Request $request)
    {
        $response   = new GetResponse();
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::MSG_GETREQ, 'Basho\Riak\ProtoBuf\RpbGetResp');

        if ( ! $rpbGetResp instanceof RpbGetResp) {
            return $response;
        }

        if ( ! $rpbGetResp->hasContent()) {
            return $response;
        }

        $response->vClock      = $rpbGetResp->getVclock()->get();
        $response->contentList = $this->createContentList($rpbGetResp->getContentList());

        return $response;
    }
}

<?php

namespace Basho\Riak\Core\Adapter\Rpb\Kv;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Kv\DeleteRequest;
use Basho\Riak\Core\Message\Kv\DeleteResponse;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\ProtoBuf\RpbDelReq;

/**
 * rpb delete implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RpbDelete extends BaseRpbStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\Kv\DeleteRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\RpbDelReq
     */
    private function createRpbMessage(DeleteRequest $request)
    {
        $rpbDelReq = new RpbDelReq();

        $rpbDelReq->setBucket($request->bucket);
        $rpbDelReq->setType($request->type);
        $rpbDelReq->setKey($request->key);

        if ($request->r !== null) {
            $rpbDelReq->setR($request->r);
        }

        if ($request->pr !== null) {
            $rpbDelReq->setPr($request->pr);
        }

        if ($request->w !== null) {
            $rpbDelReq->setW($request->w);
        }

        if ($request->rw !== null) {
            $rpbDelReq->setRw($request->rw);
        }

        if ($request->dw !== null) {
            $rpbDelReq->setDw($request->dw);
        }

        if ($request->pw !== null) {
            $rpbDelReq->setPw($request->dw);
        }

        if ($request->vClock !== null) {
            $rpbDelReq->setVclock($request->vClock);
        }

        return $rpbDelReq;
    }

    /**
     * @param \Basho\Riak\Core\Message\Kv\DeleteRequest $request
     *
     * @return \Basho\Riak\Core\Message\Kv\DeleteResponse
     */
    public function send(Request $request)
    {
        $response   = new DeleteResponse();
        $rpbPutReq  = $this->createRpbMessage($request);

        $this->client->send($rpbPutReq, RiakMessageCodes::MSG_DELREQ);

        return $response;
    }
}

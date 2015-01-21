<?php

namespace Basho\Riak\Core\Adapter\Proto\DataType;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\DtUpdateReq;
use Basho\Riak\ProtoBuf\DtUpdateResp;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Message\DataType\PutRequest;
use Basho\Riak\Core\Message\DataType\PutResponse;

/**
 * rpb put implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class ProtoPut extends BaseProtoStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\DataType\PutRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\DtUpdateReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new DtUpdateReq();
        $crdtOp    = $this->opConverter->toProtoBuf($request->op);

        $rpbPutReq->setBucket($request->bucket);
        $rpbPutReq->setType($request->type);
        $rpbPutReq->setKey($request->key);

        if ($request->w !== null) {
            $rpbPutReq->setW($request->w);
        }

        if ($request->dw !== null) {
            $rpbPutReq->setDw($request->dw);
        }

        if ($request->pw !== null) {
            $rpbPutReq->setPw($request->pw);
        }

        if ($request->returnBody !== null) {
            $rpbPutReq->setReturnBody($request->returnBody);
        }

        $rpbPutReq->setOp($crdtOp);

        return $rpbPutReq;
    }

    /**
     * @param \Basho\Riak\Core\Message\DataType\PutRequest $request
     *
     * @return \Basho\Riak\Core\Message\DataType\PutResponse
     */
    public function send(Request $request)
    {
        $response   = new PutResponse();
        $rpbPutReq  = $this->createRpbMessage($request);
        $rpbPutResp = $this->client->send($rpbPutReq, RiakMessageCodes::DT_UPDATE_REQ, RiakMessageCodes::DT_UPDATE_RESP);

        if ( ! $rpbPutResp instanceof DtUpdateResp) {
            return $response;
        }

        if ($rpbPutResp->hasCounterValue()) {
            $response->value = $rpbPutResp->counter_value;
            $response->type  = 'counter';
        }

        if ($rpbPutResp->hasSetValue()) {
            $response->value = $rpbPutResp->set_value;
            $response->type  = 'set';
        }

        if ($rpbPutResp->hasMapValue()) {
            $response->value = $rpbPutResp->map_value;
            $response->type  = 'map';
        }

        return $response;
    }
}

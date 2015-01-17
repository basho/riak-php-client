<?php

namespace Basho\Riak\Core\Adapter\Rpb\DataType;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Rpb\RpbStrategy;
use Basho\Riak\ProtoBuf\RpbCounterUpdateReq;
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
class RpbPut extends RpbStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\DataType\PutRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\RpbCounterUpdateReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new RpbCounterUpdateReq();
        $value     = $request->op->getIncrement();

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
            $rpbPutReq->setReturnvalue($request->returnBody);
        }

        $rpbPutReq->setAmount($value);

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
        $rpbPutResp = $this->client->send($rpbPutReq, RiakMessageCodes::MSG_COUNTERUPDATEREQ, RiakMessageCodes::MSG_COUNTERUPDATERESP);

        $response->value = $rpbPutResp->getValue()->get();
        $response->type  = 'counter';

        return $response;
    }
}

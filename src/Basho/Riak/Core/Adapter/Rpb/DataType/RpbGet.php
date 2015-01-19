<?php

namespace Basho\Riak\Core\Adapter\Rpb\DataType;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\DtFetchReq;
use Basho\Riak\ProtoBuf\DtFetchResp;
use Basho\Riak\ProtoBuf\DtFetchResp\DataType;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Rpb\RpbStrategy;
use Basho\Riak\Core\Message\DataType\GetRequest;
use Basho\Riak\Core\Message\DataType\GetResponse;

/**
 * rpb get implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RpbGet extends RpbStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\DataType\GetRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\DtFetchReq
     */
    private function createRpbMessage(GetRequest $request)
    {
        $rpbRequest = new DtFetchReq();

        $rpbRequest->setBucket($request->bucket);
        $rpbRequest->setType($request->type);
        $rpbRequest->setKey($request->key);

        if ($request->r !== null) {
            $rpbRequest->setR($request->r);
        }

        if ($request->pr !== null) {
            $rpbRequest->setPr($request->pr);
        }

        if ($request->basicQuorum !== null) {
            $rpbRequest->setBasicQuorum($request->basicQuorum);
        }

        if ($request->notfoundOk !== null) {
            $rpbRequest->setNotfoundOk($request->notfoundOk);
        }

        return $rpbRequest;
    }

    /**
     * @param \Basho\Riak\Core\Message\DataType\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\DataType\GetResponse
     */
    public function send(Request $request)
    {
        $response    = new GetResponse();
        $rpbRequest  = $this->createRpbMessage($request);
        $rpbResponse = $this->client->send($rpbRequest, RiakMessageCodes::MSG_DTFETCHREQ, RiakMessageCodes::MSG_DTFETCHRESP);

        if ( ! $rpbResponse instanceof DtFetchResp) {
            return $response;
        }

        $dtType  = $rpbResponse->getType();
        $dtValue = $rpbResponse->getValue()->getOrElse(null);

        if (DataType::COUNTER == $dtType && $dtValue != null) {
            $response->value = $dtValue->counter_value;
            $response->type  = 'counter';
        }

        if (DataType::SET == $dtType && $dtValue != null) {
            $response->value = $dtValue->set_value;
            $response->type  = 'set';
        }

        if (DataType::MAP == $dtType && $dtValue != null) {
            $response->value = $dtValue->map_value;
            $response->type  = 'map';
        }

        return $response;
    }
}

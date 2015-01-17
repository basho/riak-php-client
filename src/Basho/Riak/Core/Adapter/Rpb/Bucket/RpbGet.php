<?php

namespace Basho\Riak\Core\Adapter\Rpb\Bucket;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\RpbGetBucketReq;
use Basho\Riak\ProtoBuf\RpbBucketProps;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Message\Bucket\GetRequest;
use Basho\Riak\Core\Message\Bucket\GetResponse;

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
     * @param \Basho\Riak\Core\Message\Bucket\GetRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\RpbGetBucketReq
     */
    private function createRpbMessage(GetRequest $request)
    {
        $rpbGetReq = new RpbGetBucketReq();

        $rpbGetReq->setBucket($request->bucket);
        $rpbGetReq->setType($request->type);

        return $rpbGetReq;
    }

    /**
     * @param \Basho\Riak\ProtoBuf\RpbBucketProps $props
     *
     * @return \Basho\Riak\Core\Message\Bucket\GetResponse
     */
    private function createGetResponse(RpbBucketProps $props)
    {
        $response = new GetResponse();

        $response->nVal       = $props->getNVal()->get();
        $response->allowMult  = $props->getAllowMult()->get();
        $response->notfoundOk = $props->getNotfoundOk()->get();

        // @TODO - add properties

        return $response;
    }

    /**
     * @param \Basho\Riak\Core\Message\Bucket\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::MSG_GETBUCKETREQ, RiakMessageCodes::MSG_GETBUCKETRESP);
        $rpbProps   = $rpbGetResp->getProps();

        return $this->createGetResponse($rpbProps);
    }
}

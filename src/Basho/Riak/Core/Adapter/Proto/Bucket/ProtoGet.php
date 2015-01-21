<?php

namespace Basho\Riak\Core\Adapter\Proto\Bucket;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\RpbGetBucketReq;
use Basho\Riak\ProtoBuf\RpbBucketProps;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Proto\ProtoStrategy;
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
class ProtoGet extends ProtoStrategy
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

        $response->allowMult     = $props->allow_mult;
        $response->basicQuorum   = $props->basic_quorum;
        $response->bigVclock     = $props->big_vclock;
        $response->dw            = $props->dw;
        $response->lastWriteWins = $props->last_write_wins;
        $response->notfoundOk    = $props->notfound_ok;
        $response->nVal          = $props->n_val;
        $response->oldVclock     = $props->old_vclock;
        $response->pr            = $props->pr;
        $response->pw            = $props->pw;
        $response->r             = $props->r;
        $response->rw            = $props->rw;
        $response->w             = $props->w;
        $response->smallVclock   = $props->small_vclock;
        $response->youngVclock   = $props->young_vclock;

        // optional values
        $response->search       = $props->search;
        $response->searchIndex  = $props->search_index;
        $response->backend      = $props->backend;
        $response->consistent   = $props->consistent;
        $response->datatype     = $props->datatype;

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
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::GET_BUCKET_REQ, RiakMessageCodes::GET_BUCKET_RESP);
        $rpbProps   = $rpbGetResp->getProps();

        return $this->createGetResponse($rpbProps);
    }
}

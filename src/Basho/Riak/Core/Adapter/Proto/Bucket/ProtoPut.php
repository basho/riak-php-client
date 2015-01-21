<?php

namespace Basho\Riak\Core\Adapter\Proto\Bucket;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\RpbBucketProps;
use Basho\Riak\ProtoBuf\RpbSetBucketReq;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Proto\ProtoStrategy;
use Basho\Riak\Core\Message\Bucket\PutRequest;
use Basho\Riak\Core\Message\Bucket\PutResponse;

/**
 * rpb put implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class ProtoPut extends ProtoStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\Bucket\PutRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\RpbSetBucketReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new RpbSetBucketReq();
        $rpbProps  = new RpbBucketProps();

        $rpbProps->n_val           = $request->nVal;
        $rpbProps->allow_mult      = $request->allowMult;
        $rpbProps->last_write_wins = $request->lastWriteWins;
        $rpbProps->old_vclock      = $request->oldVclock;
        $rpbProps->young_vclock    = $request->youngVclock;
        $rpbProps->big_vclock      = $request->bigVclock;
        $rpbProps->small_vclock    = $request->smallVclock;
        $rpbProps->pr              = $request->pr;
        $rpbProps->r               = $request->r;
        $rpbProps->w               = $request->w;
        $rpbProps->pw              = $request->pw;
        $rpbProps->dw              = $request->dw;
        $rpbProps->rw              = $request->rw;
        $rpbProps->basic_quorum    = $request->basicQuorum;
        $rpbProps->notfound_ok     = $request->notfoundOk;
        $rpbProps->backend         = $request->backend;
        $rpbProps->search          = $request->search;
        $rpbProps->search_index    = $request->searchIndex;
        $rpbProps->datatype        = $request->datatype;
        $rpbProps->consistent      = $request->consistent;

        $rpbPutReq->setBucket($request->bucket);
        $rpbPutReq->setType($request->type);
        $rpbPutReq->setProps($rpbProps);

        return $rpbPutReq;
    }

    /**
     * @param \Basho\Riak\Core\Message\Bucket\PutRequest $request
     *
     * @return \Basho\Riak\Core\Message\Bucket\PutResponse
     */
    public function send(Request $request)
    {
        $response   = new PutResponse();
        $rpbPutReq  = $this->createRpbMessage($request);

        $this->client->send($rpbPutReq, RiakMessageCodes::SET_BUCKET_REQ, RiakMessageCodes::SET_BUCKET_RESP);

        return $response;
    }
}

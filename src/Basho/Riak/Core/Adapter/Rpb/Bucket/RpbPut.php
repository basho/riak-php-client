<?php

namespace Basho\Riak\Core\Adapter\Rpb\Bucket;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\ProtoBuf\RpbBucketProps;
use Basho\Riak\ProtoBuf\RpbSetBucketReq;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\Core\Adapter\Rpb\RpbStrategy;
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
class RpbPut extends RpbStrategy
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

        $rpbPutReq->setBucket($request->bucket);
        $rpbPutReq->setType($request->type);
        $rpbPutReq->setProps($rpbProps);

        if ($request->nVal) {
            $rpbProps->setNVal($request->nVal);
        }

        if ($request->notfoundOk) {
            $rpbProps->setNotfoundOk($request->notfoundOk);
        }

        if ($request->allowMult) {
            $rpbProps->setAllowMult($request->allowMult);
        }

        // @todo - add properties

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

        $this->client->send($rpbPutReq, RiakMessageCodes::MSG_SETBUCKETREQ, RiakMessageCodes::MSG_SETBUCKETRESP);

        return $response;
    }
}

<?php

namespace Basho\Riak\Core\Adapter\Rpb\Kv;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Kv\PutRequest;
use Basho\Riak\Core\Message\Kv\PutResponse;
use Basho\Riak\ProtoBuf\RiakMessageCodes;
use Basho\Riak\ProtoBuf\RpbPutReq;
use Basho\Riak\ProtoBuf\RpbContent;
use Basho\Riak\ProtoBuf\RpbPair;

/**
 * rpb put implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RpbPut extends BaseRpbStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\Kv\PutRequest $request
     *
     * @return \Basho\Riak\ProtoBuf\RpbPutReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new RpbPutReq();

        $rpbPutReq->setVclock($request->vClock);
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
            $rpbPutReq->setPw($request->dw);
        }

        if ($request->returnBody !== null) {
            $rpbPutReq->setReturnBody($request->returnBody);
        }

        if ($request->vClock !== null) {
            $rpbPutReq->setVclock($request->vClock);
        }

        if ( ! $request->content) {
            return $rpbPutReq;
        }

        $rpbContent = new RpbContent();

        $rpbContent->setVtag($request->content->vtag);
        $rpbContent->setValue($request->content->value);
        $rpbContent->setContentType($request->content->contentType);

        foreach ($request->content->indexes as $name => $values) {
            // @TODO
        }

        foreach ($request->content->metas as $name => $meta) {
            $value = new RpbPair();

            $value->setKey($name);
            $value->setValue($meta);

            $rpbContent->addUsermeta($value);
        }

        $rpbPutReq->setContent($rpbContent);

        return $rpbPutReq;
    }

    /**
     * @param \Basho\Riak\Core\Message\Kv\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\Kv\PutResponse
     */
    public function send(Request $request)
    {
        $response   = new PutResponse();
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::MSG_PUTREQ, RiakMessageCodes::MSG_PUTRESP);

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

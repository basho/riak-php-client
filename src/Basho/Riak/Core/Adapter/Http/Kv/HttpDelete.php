<?php

namespace Basho\Riak\Core\Adapter\Http\Kv;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Kv\DeleteRequest;
use Basho\Riak\Core\Message\Kv\DeleteResponse;

/**
 * Http delete implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class HttpDelete extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true,
        204 => true,
        404 => true,
    ];

    /**
     * @param \Basho\Riak\Core\Message\Kv\DeleteRequest $deleteRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    public function createHttpRequest(DeleteRequest $deleteRequest)
    {
        $request  = $this->createRequest('DELETE', $deleteRequest->type, $deleteRequest->bucket, $deleteRequest->key);
        $query    = $request->getQuery();

        $request->setHeader('Accept', ['multipart/mixed', '*/*']);

        if ($deleteRequest->r !== null) {
            $query->add('r', $deleteRequest->r);
        }

        if ($deleteRequest->pr !== null) {
            $query->add('pr', $deleteRequest->pr);
        }

        if ($deleteRequest->rw !== null) {
            $query->add('rw', $deleteRequest->rw);
        }

        if ($deleteRequest->w !== null) {
            $query->add('w', $deleteRequest->w);
        }

        if ($deleteRequest->dw !== null) {
            $query->add('dw', $deleteRequest->dw);
        }

        if ($deleteRequest->pw !== null) {
            $query->add('pw', $deleteRequest->pw);
        }

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Message\Kv\DeleteRequest $request
     *
     * @return \Basho\Riak\Core\Message\Kv\DeleteResponse
     */
    public function send(Request $request)
    {
        $httpRequest = $this->createHttpRequest($request);
        $response    = new DeleteResponse();
        $vClock      = $request->vClock;

        if ($vClock) {
            $httpRequest->setHeader('X-Riak-Vclock', $vClock);
        }

        try {
            $httpResponse = $this->client->send($httpRequest);
            $code         = $httpResponse->getStatusCode();
        } catch (RequestException $e) {
            if ($e->getCode() == 404 && $request->notfoundOk) {
                return;
            }

            throw $e;
        }

        if (isset($this->validResponseCodes[$code])) {
            $contentList = $this->getRiakContentList($httpResponse);
            $vClock      = $httpResponse->getHeader('X-Riak-Vclock');

            $response->vClock = $vClock;
            $response->contentList = $contentList;
        }

        return $response;
    }
}

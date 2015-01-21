<?php

namespace Basho\Riak\Core\Adapter\Http\Bucket;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Bucket\PutRequest;
use Basho\Riak\Core\Message\Bucket\PutResponse;
use GuzzleHttp\Stream\Stream;

/**
 * Http put implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class HttpPut extends BaseHttpStrategy
{
    /**
     * @param \Basho\Riak\Core\Message\DataType\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutRequest $putRequest)
    {
        $request = $this->createRequest('PUT', $putRequest->type, $putRequest->bucket);
        $props   = $this->requestToArray($putRequest);

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory(json_encode([
            'props' => $props
        ])));

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Message\DataType\PutRequest $request
     *
     * @return \Basho\Riak\Core\Message\DataType\PutResponse
     */
    public function send(Request $request)
    {
        $response     = new PutResponse();
        $httpRequest  = $this->createHttpRequest($request);

        $this->client->send($httpRequest);

        return $response;
    }

    /**
     * @param \Basho\Riak\Core\Message\Request $request
     *
     * @return \Basho\Riak\Core\Message\Request
     */
    public function requestToArray(Request $request)
    {
        $values = [
            'allow_mult'      => $request->allowMult,
            'backend'         => $request->backend,
            'basic_quorum'    => $request->basicQuorum,
            'big_vclock'      => $request->bigVclock,
            'consistent'      => $request->consistent,
            'datatype'        => $request->datatype,
            'dw'              => $request->dw,
            'last_write_wins' => $request->lastWriteWins,
            'notfound_ok'     => $request->notfoundOk,
            'n_val'           => $request->nVal,
            'old_vclock'      => $request->oldVclock,
            'pr'              => $request->pr,
            'pw'              => $request->pw,
            'r'               => $request->r,
            'rw'              => $request->rw,
            'w'               => $request->w,
            'search'          => $request->search,
            'search_index'    => $request->searchIndex,
            'small_vclock'    => $request->smallVclock,
            'young_vclock'    => $request->youngVclock,
        ];

        return array_filter($values);
    }
}

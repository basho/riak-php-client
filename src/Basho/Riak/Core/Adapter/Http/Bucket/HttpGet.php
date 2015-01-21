<?php

namespace Basho\Riak\Core\Adapter\Http\Bucket;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Bucket\GetRequest;
use Basho\Riak\Core\Message\Bucket\GetResponse;

/**
 * Http get implementation.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class HttpGet extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true
    ];

    /**
     * @param \Basho\Riak\Core\Message\DataType\GetRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetRequest $getRequest)
    {
        $request = $this->createRequest('GET', $getRequest->type, $getRequest->bucket);

        $request->setHeader('Accept', ['multipart/mixed', 'application/json']);

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Message\Bucket\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if (isset($this->validResponseCodes[$code])) {
            $json  = $httpResponse->json();
            $props = $json['props'];

            return $this->createGetResponse($props);
        }

        return $this->createGetResponse([]);
    }

    /**
     * @param array $props
     *
     * @return \Basho\Riak\Core\Message\Bucket\GetResponse
     */
    public function createGetResponse(array $props)
    {
        $response = new GetResponse();

        $response->allowMult     = $props['allow_mult'];
        $response->basicQuorum   = $props['basic_quorum'];
        $response->bigVclock     = $props['big_vclock'];
        $response->dw            = $props['dw'];
        $response->lastWriteWins = $props['last_write_wins'];
        $response->notfoundOk    = $props['notfound_ok'];
        $response->nVal          = $props['n_val'];
        $response->oldVclock     = $props['old_vclock'];
        $response->pr            = $props['pr'];
        $response->pw            = $props['pw'];
        $response->r             = $props['r'];
        $response->rw            = $props['rw'];
        $response->w             = $props['w'];
        $response->smallVclock   = $props['small_vclock'];
        $response->youngVclock   = $props['young_vclock'];

        // optional values
        $response->search      = isset($props['search']) ? $props['search'] : null;
        $response->backend     = isset($props['backend']) ? $props['backend'] : null;
        $response->datatype    = isset($props['datatype']) ? $props['datatype'] : null;
        $response->consistent  = isset($props['consistent']) ? $props['consistent'] : null;
        $response->searchIndex = isset($props['search_index']) ? $props['search_index'] : null;

        return $response;
    }
}

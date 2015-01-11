<?php

namespace Basho\Riak\Core\Adapter\Http\DataType;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\DataType\GetRequest;
use Basho\Riak\Core\Message\DataType\GetResponse;
use GuzzleHttp\Exception\RequestException;

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
        200 => true,
        300 => true
    ];

    /**
     * @param \Basho\Riak\Core\Message\DataType\GetRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetRequest $getRequest)
    {
        $request = $this->createRequest('GET', $getRequest->type, $getRequest->bucket, $getRequest->key);
        $query   = $request->getQuery();

        $request->setHeader('Accept', ['multipart/mixed', 'application/json']);

        if ($getRequest->r !== null) {
            $query->add('r', $getRequest->r);
        }

        if ($getRequest->pr !== null) {
            $query->add('pr', $getRequest->pr);
        }

        if ($getRequest->basicQuorum !== null) {
            $query->add('basic_quorum', $getRequest->basicQuorum ? 'true' : 'false');
        }

        if ($getRequest->notfoundOk !== null) {
            $query->add('notfound_ok', $getRequest->notfoundOk ? 'true' : 'false');
        }

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Message\DataType\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\DataType\GetResponse
     */
    public function send(Request $request)
    {
        $response    = new GetResponse();
        $httpRequest = $this->createHttpRequest($request);

        try {
            $httpResponse = $this->client->send($httpRequest);
            $code         = $httpResponse->getStatusCode();
        } catch (RequestException $e) {
            if ($e->getCode() == 404 && $request->notfoundOk) {
                $response->value = 0;
                $response->type  = 'counter';

                return $response;
            }

            throw $e;
        }

        if (isset($this->validResponseCodes[$code])) {
            $json = $httpResponse->json();

            $response->value = $json['value'];
            $response->type  = $json['type'];
        }

        return $response;
    }
}

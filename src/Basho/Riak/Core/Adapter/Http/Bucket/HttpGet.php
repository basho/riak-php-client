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
        $callback = function ($c) {
            return strtoupper($c[1]);
        };

        foreach ($props as $key => $value) {
            $name  = preg_replace_callback('/_([a-z])/', $callback, $key);

            $response->{$name} = $value;
        }

        return $response;
    }
}

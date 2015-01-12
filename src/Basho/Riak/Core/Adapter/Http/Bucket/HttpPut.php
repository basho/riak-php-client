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
        $values = [];

        foreach ($request as $key => $value) {
            if ($value === null) {
                continue;
            }

            $unde = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $key);
            $name = strtolower($unde);

            $values[$name] = $value;
        }

        if (isset($values['bucket'])) {
            unset($values['bucket']);
        }

        if (isset($values['type'])) {
            unset($values['type']);
        }

        return $values;
    }
}

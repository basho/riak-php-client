<?php

namespace Basho\Riak\Core\Adapter\Http\DataType;

use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\DataType\PutRequest;
use Basho\Riak\Core\Message\DataType\PutResponse;
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
     * @var array
     */
    protected $validResponseCodes = [
        200 => true
    ];

    /**
     * @param \Basho\Riak\Core\Message\DataType\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutRequest $putRequest)
    {
        $request   = $this->createRequest('POST', $putRequest->type, $putRequest->bucket, $putRequest->key);
        $increment = $putRequest->op->getIncrement();
        $query     = $request->getQuery();

        $request->setHeader('Accept', ['multipart/mixed', 'application/json']);
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory((string) $increment));

        if ($putRequest->w !== null) {
            $query->add('w', $putRequest->w);
        }

        if ($putRequest->dw !== null) {
            $query->add('dw', $putRequest->dw);
        }

        if ($putRequest->pw !== null) {
            $query->add('pw', $putRequest->pw);
        }

        if ($putRequest->returnBody !== null) {
            $query->add('returnbody', $putRequest->returnBody ? 'true' : 'false');
        }

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
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if (isset($this->validResponseCodes[$code])) {
            $json = $httpResponse->json();

            $response->value = $json['value'];
            $response->type  = $json['type'];
        }

        return $response;
    }
}

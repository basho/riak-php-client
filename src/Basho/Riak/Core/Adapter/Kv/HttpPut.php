<?php

namespace Basho\Riak\Core\Adapter\Kv;

use GuzzleHttp\Stream\Stream;
use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\PutRequest;
use Basho\Riak\Core\Message\PutResponse;

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
        200 => true,
        201 => true,
        204 => true,
        300 => true
    ];

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);
    }

    /**
     * @param \Basho\Riak\Core\Message\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    public function createHttpRequest(PutRequest $putRequest)
    {
        $request     = $this->createRequest('PUT', $putRequest->type, $putRequest->bucket, $putRequest->key);
        $query       = $request->getQuery();
        $content     = $putRequest->content;
        $contentType = $content['contentType'];
        $value       = $content['value'];

        $request->setHeader('Accept', ['multipart/mixed', '*/*']);
        $request->setHeader('Content-Type', $contentType);
        $request->setBody(Stream::factory($value));

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

        if ($putRequest->vClock !== null) {
            $request->setHeader('X-Riak-Vclock', $putRequest->vClock);
        }

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Message\PutRequest $request
     *
     * @return \Basho\Riak\Core\Message\PutResponse
     */
    public function send(Request $request)
    {
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();
        $response     = new PutResponse();

        if (isset($this->validResponseCodes[$code])) {
            $contentList = $this->getRiakContentList($httpResponse);
            $vClock      = $httpResponse->getHeader('X-Riak-Vclock');

            $response->vClock = $vClock;
            $response->contentList = $contentList;
        }

        return $response;
    }
}

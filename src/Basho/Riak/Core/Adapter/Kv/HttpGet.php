<?php

namespace Basho\Riak\Core\Adapter\Kv;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Message\Request;
use Basho\Riak\Core\Message\Kv\GetRequest;
use Basho\Riak\Core\Message\Kv\GetResponse;
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
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);
    }

    /**
     * @param \Basho\Riak\Core\Message\Kv\GetRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetRequest $getRequest)
    {
        $request = $this->createRequest('GET', $getRequest->type, $getRequest->bucket, $getRequest->key);
        $query   = $request->getQuery();

        $request->setHeader('Accept', ['multipart/mixed', '*/*']);

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
     * @param \Basho\Riak\Core\Message\Kv\GetRequest $request
     *
     * @return \Basho\Riak\Core\Message\Kv\GetResponse
     */
    public function send(Request $request)
    {
        try {
            $httpRequest  = $this->createHttpRequest($request);
            $httpResponse = $this->client->send($httpRequest);
            $code         = $httpResponse->getStatusCode();
            $response     = new GetResponse();
        } catch (RequestException $e) {
            if ($e->getCode() == 404) {
                return null;
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

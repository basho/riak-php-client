<?php

namespace Basho\Riak\Core\Adapter\Kv;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Adapter\Strategy;
use GuzzleHttp\Message\ResponseInterface;
use Basho\Riak\Core\Adapter\Http\MultipartResponseIterator;

/**
 * Base http strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseHttpStrategy implements Strategy
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $validResponseCodes = [];

    private static $headersMap = [
        'Content-Type'  => 'contentType',
        'Last-Modified' => 'lastModified',
        'Etag'          => 'vtag',
    ];

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return string
     */
    protected function buildPath($type, $bucket, $key)
    {
        if ($type === null) {
            return sprintf('/buckets/%s/keys/%s', $bucket, $key);
        }

        return sprintf('/types/%s/buckets/%s/keys/%s', $type, $bucket, $key);
    }

    /**
     * @param string $method
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $type, $bucket, $key)
    {
        $path    = $this->buildPath($type, $bucket, $key);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function parseHeaders(array $headers)
    {
        $values = [];

        foreach (self::$headersMap as $key => $name) {
            if ( ! isset($headers[$key])) {
                continue;
            }

            $values[$name] = is_array($headers[$key])
                ? reset($headers[$key])
                : $headers[$key];
        }

        return $values;
    }

    /**
     * Parse multipart content (Shoud be part of GuzzleHttp !!)
     *
     * @param \GuzzleHttp\Message\ResponseInterface $response
     *
     * @return array
     */
    protected function getMultipartBodyContent(ResponseInterface $response)
    {
        $list     = [];
        $iterator = new MultipartResponseIterator($response);

        foreach ($iterator as $response) {
            $list[] = $this->createObjectMap($response->getHeaders(), $response->getBody());
        }

        return $list;
    }

    /**
     * @param \GuzzleHttp\Message\ResponseInterface $response
     *
     * @return array
     */
    protected function getBodyContent(ResponseInterface $response)
    {
        $body    = $response->getBody();
        $headers = $response->getHeaders();

        return $this->createObjectMap($headers, $body);
    }

    /**
     * @param array  $headers
     * @param string $value
     *
     * @return array
     */
    protected function createObjectMap(array $headers, $value)
    {
        return array_merge($this->parseHeaders($headers), ['value' => $value]);
    }

    /**
     * @param \GuzzleHttp\Message\ResponseInterface $response
     *
     * @return array
     */
    protected function getRiakContentList(ResponseInterface $response)
    {
        $contentList = [];
        $code        = $response->getStatusCode();

        if ($code == 300) {
            $contentList = $this->getMultipartBodyContent($response);
        }

        if ($code == 200) {
            $contentList[] = $this->getBodyContent($response);
        }

        return $contentList;
    }
}

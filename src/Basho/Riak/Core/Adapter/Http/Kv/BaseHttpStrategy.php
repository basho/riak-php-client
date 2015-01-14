<?php

namespace Basho\Riak\Core\Adapter\Http\Kv;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Adapter\Strategy;
use Basho\Riak\Core\Message\Kv\Content;
use GuzzleHttp\Message\ResponseInterface;
use Basho\Riak\Core\Adapter\Http\MultipartResponseIterator;

/**
 * Base http strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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
    protected function parseIndexHeaders(array $headers)
    {
        $values  = [];

        foreach ($headers as $key => $value) {
            if (strpos($key, 'x-riak-index-') !== 0) {
                continue;
            }

            foreach ($value as $val) {
                $values[substr($key, 13)][] = $val;
            }
        }

        return $values;
    }

    /**
     * @param string $key
     * @param array  $headers
     * @param mixed  $default
     *
     * @return array
     */
    protected function firstHeader($key, array $headers, $default = null)
    {
        if ( ! isset($headers[$key])) {
            return $default;
        }

        return is_array($headers[$key])
            ? reset($headers[$key])
            : $headers[$key];
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
            $list[] = $this->createContent($response->getHeaders(), $response->getBody());
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

        return $this->createContent($headers, $body);
    }

    /**
     * @param array  $headers
     * @param string $value
     *
     * @return array
     */
    private function createContent(array $headers, $value)
    {
        $content = new Content();
        $indexes = $this->parseIndexHeaders($headers);

        $content->lastModified = $this->firstHeader('Last-Modified', $headers);
        $content->contentType  = $this->firstHeader('Content-Type', $headers);
        $content->vtag         = $this->firstHeader('Etag', $headers);
        $content->indexes      = $indexes;
        $content->value        = $value;

        return $content;
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

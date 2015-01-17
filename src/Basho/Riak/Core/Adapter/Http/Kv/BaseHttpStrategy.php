<?php

namespace Basho\Riak\Core\Adapter\Http\Kv;

use Basho\Riak\Core\Message\Kv\Content;
use GuzzleHttp\Message\ResponseInterface;
use Basho\Riak\Core\Adapter\Http\HttpStrategy;
use Basho\Riak\Core\Adapter\Http\MultipartResponseIterator;

/**
 * Base http strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseHttpStrategy extends HttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [];

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
    private function parseHeaders(array $headers)
    {
        $indexes = [];
        $metas   = [];

        foreach ($headers as $key => $value) {
            $key = strtolower($key);

            if (strpos($key, 'x-riak-index-') === 0) {
                $name  = substr($key, 13);
                $value = isset($indexes[$name])
                    ? array_merge($indexes[$name], $value)
                    : $value;

                $indexes[$name] = $value;
            }

            if (strpos($key, 'x-riak-meta-') === 0) {
                $name  = strtolower(substr($key, 12));
                $value = is_array($value)
                    ? reset($value)
                    : $value;

                $metas[$name] = $value;
            }
        }

        return [
            'indexes' => $indexes,
            'metas'   => $metas
        ];
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
        $result  = $this->parseHeaders($headers);

        $content->lastModified = $this->firstHeader('Last-Modified', $headers);
        $content->contentType  = $this->firstHeader('Content-Type', $headers);
        $content->vtag         = $this->firstHeader('Etag', $headers);
        $content->indexes      = $result['indexes'];
        $content->metas        = $result['metas'];
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

<?php

namespace Basho\Riak\Command;

/**
 * Data structure for handling Command responses from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response
{
    /**
     * Response headers returned from request
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Response body returned from request
     *
     * @var string
     */
    protected $body = '';

    /**
     * HTTP Status Code from response
     *
     * @var int
     */
    protected $statusCode = 0;

    public function __construct($statusCode, $headers = [], $body = '')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getContentType()
    {
        return $this->getHeader('Content-Type');
    }

    /**
     * Retrieve the value for a header
     *
     * @param $key
     *
     * @return string
     * @throws Exception
     */
    protected function getHeader($key)
    {
        if (!isset($this->headers[$key])) {
            throw new Exception("Header with key, {$key}, not available within response object.");
        }

        return $this->headers[$key];
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->statusCode == '404' ? true : false;
    }

    /**
     * @return bool
     */
    public function isUnauthorized()
    {
        return $this->statusCode == '401' ? true : false;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return in_array($this->statusCode, ['200', '201', '204']) ? true : false;
    }

    /**
     * Added for backwards compatibility with the Zend Z-Ray plugin, provides error message in versions 3.0+
     *
     * @return string
     */
    public function getMessage()
    {
        return '';
    }
}

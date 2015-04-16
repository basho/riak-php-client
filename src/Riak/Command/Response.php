<?php

/*
Copyright 2015 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

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
}
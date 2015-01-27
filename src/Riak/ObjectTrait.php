<?php

/*
Copyright 2014 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Basho\Riak;

/**
 * Trait ObjectTrait
 *
 * Offers code reuse between kv objects & crdts since they several common needs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait ObjectTrait
{
    /**
     * Key for the object
     *
     * @var string
     */
    protected $key = '';

    /**
     * Request / response headers for the object
     *
     * Content type, last modified, etc
     *
     * @var array
     */
    protected $headers = [
        'content-type' => 'application/json',
    ];

    /**
     * @var Bucket|null
     */
    protected $bucket = null;

    /**
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param Bucket|null $bucket
     *
     * @return $this
     */
    public function setBucket(Bucket $bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getKey();
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * setHeader
     *
     * Sets a single header value within the headers array
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * getHeader
     *
     * Retrieve the value for a header, null if not set
     *
     * @param $key
     * @return string|null
     */
    public function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
    }
}
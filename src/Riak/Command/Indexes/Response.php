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

namespace Basho\Riak\Command\Indexes;


/**
 * Container for a response related to an index query
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var array
     */
    protected $results = [];


    /**
     * @var bool
     */
    protected $termsReturned = false;

    protected $done = false;

    /**
     * @var string|null
     */
    protected $continuation = null;

    public function __construct($statusCode, $headers = [], $body = '')
    {
        parent::__construct($statusCode, $headers, $body);

        // make sure body is not only whitespace
        if (trim($body)) {
            $this->decodeBody($body);
        }
    }

    private function decodeBody($body)
    {
        $body = json_decode(rawurldecode($body), true);

        if (isset($body['keys'])) {
            $this->results = $body['keys'];
        }

        if (isset($body['results'])) {
            $this->results = $body['results'];
            $this->termsReturned = true;
        }

        if (isset($body['continuation'])) {
            $this->continuation = $body['continuation'];
            $this->done = false;
        } else {
            $this->done = true;
        }
    }

    /**
     * Get the array of keys that match the query
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Indicates whether the terms are included in the results array.
     *
     * @return bool
     */
    public function hasReturnTerms()
    {
        return $this->termsReturned;
    }

    /**
     * Get the continuation string for paged queries.
     *
     * @return null|string
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->statusCode == '200' ? TRUE : FALSE;
    }

    /**
     * Retrieves the date of the object's retrieval
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getDate()
    {
        return $this->getHeader('Date');
    }

    public function isDone()
    {
        return $this->done;
    }
}
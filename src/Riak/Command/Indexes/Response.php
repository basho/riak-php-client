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

    protected $date = '';

    public function __construct($success = true, $code = 0, $message = '', $results = [], $termsReturned = false, $continuation = null, $done = true, $date = '')
    {
        parent::__construct($success, $code, $message);

        $this->results = $results;
        $this->termsReturned = $termsReturned;
        $this->continuation = $continuation;
        $this->done = $done;
        $this->date = $date;

        // when timeout is used, cURL returns success for some reason
        if (in_array($code, ['501', '503'])) {
            $this->success = false;
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
     * Retrieves the date of the counter's retrieval
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getDate()
    {
        return $this->date;
    }

    public function isDone()
    {
        return $this->done;
    }
}
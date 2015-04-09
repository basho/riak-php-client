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

namespace Basho\Riak\Command\Object;

use Basho\Riak\Location;
use Basho\Riak\Object;

/**
 * Class Object\Response
 *
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\Object|null
     */
    protected $object = NULL;

    public function __construct($statusCode, $headers = [], $body = '')
    {
        parent::__construct($statusCode, $headers, $body);

        // make sure body is not only whitespace
        if (trim($body)) {
            $data = '';
            if ($headers['Content-Type'] == 'application/json') {
                $data = json_decode($this->body);
            } else {
                $data = rawurldecode($this->body);
            }
            $this->object = new Object($data, $this->headers);
        }
    }

    /**
     * Retrieves the Vclock value from the response headers
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getVclock()
    {
        return $this->getHeader('X-Riak-Vclock');
    }

    /**
     * Retrieves the Location value from the response headers
     *
     * @return Location
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLocation()
    {
        return Location::fromString($this->getHeader('Location'));
    }

    /**
     * @return Object|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->statusCode == '404' ? TRUE : FALSE;
    }

    /**
     * @return bool
     */
    public function hasSiblings()
    {
        return $this->statusCode == '300' ? TRUE : FALSE;
    }

    /**
     * Fetches the sibling tags from the response
     *
     * @return array
     */
    public function getSiblingTags()
    {
        return [];
    }

    /**
     * Retrieves the last modified time of the object
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLastModified()
    {
        return $this->getHeader('Last-Modified');
    }

    /**
     * Retrieves the etag of the object
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getETag()
    {
        return $this->getHeader('ETag');
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
}
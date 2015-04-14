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

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Bucket;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * Bucket from the command re-instantiated with its fetched properties
     *
     * @var Bucket|null
     */
    protected $bucket = null;

    public function __construct($statusCode, $headers = [], $body = '', Bucket $bucket = null)
    {
        parent::__construct($statusCode, $headers, $body);

        // make sure body is not only whitespace
        if (trim($body)) {
            $properties = json_decode($this->body, true);
            if ($bucket) {
                $this->bucket = new Bucket($bucket->getName(), $bucket->getType(), $properties['props']);
            }
        }
    }

    /**
     * getBucket
     *
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->statusCode == '404' ? true : false;
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
}
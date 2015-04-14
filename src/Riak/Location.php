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
 * Immutable data structure storing the location of an Object or DataType
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Location
{
    /**
     * Kv Object / CRDT key
     *
     * @var string
     */
    protected $key = '';

    /**
     * @var Bucket|null
     */
    protected $bucket = NULL;

    /**
     * @param $key
     * @param Bucket $bucket
     */
    public function __construct($key, Bucket $bucket)
    {
        $this->key = $key;
        $this->bucket = $bucket;
    }

    /**
     * Generate an instance of the Location object using the Location header string value returned from Riak
     *
     * @param $location_string
     *
     * @return Location
     */
    public static function fromString($location_string)
    {
        preg_match('/^\/types\/([^\/]+)\/buckets\/([^\/]+)\/keys\/([^\/]+)$/', $location_string, $matches);

        return new self($matches[3], new Bucket($matches[2], $matches[1]));
    }

    public function __toString()
    {
        return $this->bucket . $this->key;
    }

    /**
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
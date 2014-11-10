<?php

/*
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
 * Link
 *
 * @category   Basho
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class Link
{
    /**
     * Construct a Link object.
     *
     * @param string $bucket - The bucket name.
     * @param string $key - The key.
     * @param string $tag - The tag.
     */
    public function __construct($bucket, $key, $tag = null)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->tag = $tag;
        $this->client = null;
    }

    /**
     * Retrieve the DataType to which this link points.
     *
     * @param integer $r - The R-value to use.
     * @return DataType
     */
    public function get($r = null)
    {
        return $this->client->bucket($this->bucket)->get($this->key, $r);
    }

    /**
     * Retrieve the DataType to which this link points, as a binary.
     *
     * @param integer $r - The R-value to use.
     * @return DataType
     */
    public function getBinary($r = null)
    {
        return $this->client->bucket($this->bucket)->getBinary($this->key, $r);
    }

    /**
     * Get the bucket name of this link.
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Set the bucket name of this link.
     *
     * @param string $name - The bucket name.
     * @return $this
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * Get the key of this link.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the key of this link.
     *
     * @param string $key - The key.
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Set the tag of this link.
     *
     * @param string $tag - The tag.
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Convert this Link object to a link header string. Used internally.
     */
    public function toLinkHeader($client)
    {
        $link = "</" .
            $client->prefix . "/" .
            urlencode($this->bucket) . "/" .
            urlencode($this->key) . ">; riaktag=\"" .
            urlencode($this->getTag()) . "\"";

        return $link;
    }

    /**
     * Get the tag of this link.
     *
     * @return string
     */
    public function getTag()
    {
        if ($this->tag == null) {
            return $this->bucket;
        } else {
            return $this->tag;
        }
    }

    /**
     * Return true if the links are equal.
     *
     * @param Link $link - A Link object.
     * @return boolean
     */
    public function isEqual($link)
    {
        $is_equal =
            ($this->bucket == $link->bucket) &&
            ($this->key == $link->key) &&
            ($this->getTag() == $link->getTag());

        return $is_equal;
    }
}
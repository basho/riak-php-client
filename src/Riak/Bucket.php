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

use Basho\Riak\Link;

/**
 * Bucket
 *
 * @category   Basho
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class Bucket
{
    /**
     * Construct a Bucket object
     *
     * @param \Basho\Riak\Riak $client Riak Client object
     * @param string $name Bucket name
     */
    public function __construct($client, $name)
    {
        $this->client = $client;
        $this->name = $name;
        $this->r = null;
        $this->w = null;
        $this->dw = null;
    }

    /**
     * Get the bucket name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the R-value for this bucket
     *
     * get(...) and getBinary(...)
     * operations that do not specify an R-value will use this value.
     *
     * @see \Basho\Riak\Bucket::get()
     * @see \Basho\Riak\Bucket::getBinary()
     * @param integer $r - The new R-value.
     * @return $this
     */
    public function setR($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get the W-value for this bucket
     *
     * If it is set for this bucket, otherwise return
     * the W-value for the client.
     *
     * @return integer
     */
    public function getW($w)
    {
        if ($w != null) {
            return $w;
        }

        if ($this->w != null) {
            return $this->w;
        }

        return $this->client->getW();
    }

    /**
     * Set the W-value for this bucket
     *
     * See setR(...) for more information.
     *
     * @param  integer $w - The new W-value.
     * @return $this
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get the DW-value for this bucket
     *
     * If it is set for this bucket, otherwise return
     * the DW-value for the client.
     *
     * @return integer
     */
    public function getDW($dw)
    {
        if ($dw != null) {
            return $dw;
        }

        if ($this->dw != null) {
            return $this->dw;
        }

        return $this->client->getDW();
    }

    /**
     * Set the DW-value for this bucket.
     *
     * See setR(...) for more information.
     *
     * @param  integer $dw - The new DW-value
     * @return $this
     */
    public function setDW($dw)
    {
        $this->dw = $dw;

        return $this;
    }

    /**
     * Create a new Riak object that will be stored as JSON.
     *
     * @param  string $key - Name of the key.
     * @param  object $data - The data to store. (default NULL)
     * @return Object
     */
    public function newObject($key, $data = null)
    {
        $obj = new Object($this->client, $this, $key);
        $obj->setData($data);
        $obj->setContentType('application/json');
        $obj->jsonize = true;

        return $obj;
    }

    /**
     * Create a new Riak object that will be stored as plain text/binary.
     *
     * @param  string $key  - Name of the key.
     * @param  object $data - The data to store.
     * @param  string $content_type - The content type of the object. (default 'application/json')
     * @return Object
     */
    public function newBinary($key, $data, $content_type = 'application/json')
    {
        $obj = new Object($this->client, $this, $key);
        $obj->setData($data);
        $obj->setContentType($content_type);
        $obj->jsonize = false;

        return $obj;
    }

    /**
     * Retrieve a JSON-encoded object from Riak.
     *
     * @param  string $key - Name of the key.
     * @param  int $r - R-Value of the request (defaults to bucket's R)
     * @return Object
     */
    public function get($key, $r = null)
    {
        $obj = new Object($this->client, $this, $key);
        $obj->jsonize = true;
        $r   = $this->getR($r);

        return $obj->reload($r);
    }

    /**
     * Get the R-value for this bucket
     *
     * Returns the buckets R-value If it is set,
     * otherwise return the R-value for the client.
     *
     * @return integer
     */
    public function getR($r = null)
    {
        if ($r != null) {
            return $r;
        }

        if ($this->r != null) {
            return $this->r;
        }

        return $this->client->getR();
    }

    /**
     * Retrieve a binary/string object from Riak.
     *
     * @param  string $key - Name of the key.
     * @param  int $r - R-Value of the request (defaults to bucket's R)
     * @return Object
     */
    public function getBinary($key, $r = null)
    {
        $obj = new Object($this->client, $this, $key);
        $obj->jsonize = false;
        $r   = $this->getR($r);

        return $obj->reload($r);
    }

    /**
     * Set the N-value for this bucket
     *
     * The N-value is the number of replicas
     * that will be written of each object in the bucket. Set this once
     * before you write any data to the bucket, and never change it
     * again, otherwise unpredictable things could happen. This should
     * only be used if you know what you are doing.
     *
     * @param integer $nval - The new N-Val.
     */
    public function setNVal($nval)
    {
        return $this->setProperty("n_val", $nval);
    }

    /**
     * Set a bucket property
     *
     * This should only be used if you know what you are doing.
     *
     * @param  string $key   - Property to set.
     * @param  mixed  $value - Property value.
     */
    public function setProperty($key, $value)
    {
        return $this->setProperties([$key => $value]);
    }

    /**
     * Set multiple bucket properties in one call
     *
     * This should only be used if you know what you are doing.
     *
     * @param  array $props - An associative array of $key=>$value.
     */
    public function setProperties($props)
    {
        # Construct the URL, Headers, and Content...
        $url     = Utils::buildRestPath($this->client, $this);
        $headers = ['Content-Type: application/json'];
        $content = json_encode(["props" => $props]);

        # Run the request...
        $response = Utils::httpRequest('PUT', $url, $headers, $content);

        # Handle the response...
        if ($response == null) {
            throw new Exception("Error setting bucket properties.");
        }

        # Check the response value...
        $status = $response[0]['http_code'];
        if ($status != 204) {
            throw new Exception("Error setting bucket properties.");
        }
    }

    /**
     * Retrieve the N-value for this bucket.
     *
     * @return integer
     */
    public function getNVal()
    {
        return $this->getProperty("n_val");
    }

    /**
     * Retrieve a bucket property.
     *
     * @param string $key - The property to retrieve.
     * @return mixed
     */
    public function getProperty($key)
    {
        $props = $this->getProperties();
        if (array_key_exists($key, $props)) {
            return $props[$key];
        } else {
            return null;
        }
    }

    /**
     * Retrieve an associative array of all bucket properties.
     *
     * @return Array
     */
    public function getProperties()
    {
        # Run the request...
        $params = ['props' => 'true', 'keys' => 'false'];
        $url    = Utils::buildRestPath($this->client, $this, null, null, $params);
        $response = Utils::httpRequest('GET', $url);

        # Use a Object to interpret the response, we are just interested in the value.
        $obj = new Object($this->client, $this, null);
        $obj->populate($response, [200]);
        if (!$obj->exists()) {
            throw new Exception("Error getting bucket properties.");
        }

        $props = $obj->getData();
        $props = $props["props"];

        return $props;
    }

    /**
     * Allow conflicting data to be stored
     *
     * If set to true, then writes with conflicting data will be stored
     * and returned to the client. This situation can be detected by
     * calling hasSiblings() and getSiblings(). This should only be used
     * if you know what you are doing.
     *
     * @param  boolean $bool - True to store and return conflicting writes.
     */
    public function setAllowMultiples($bool)
    {
        return $this->setProperty("allow_mult", $bool);
    }

    /**
     * Retrieve the 'allow multiples' setting.
     *
     * @return Boolean
     */
    public function getAllowMultiples()
    {
        return "true" == $this->getProperty("allow_mult");
    }

    /**
     * Retrieve an array of all keys in this bucket.
     *
     * Note: this operation is pretty slow.
     *
     * @return Array
     */
    public function getKeys()
    {
        $params = ['props' => 'false', 'keys' => 'true'];
        $url    = Utils::buildRestPath($this->client, $this, null, null, $params);
        $response = Utils::httpRequest('GET', $url);

        # Use a Object to interpret the response, we are just interested in the value.
        $obj = new Object($this->client, $this, null);
        $obj->populate($response, [200]);
        if (!$obj->exists()) {
            throw new Exception("Error getting bucket properties.");
        }
        $keys = $obj->getData();

        return array_map("urldecode", $keys["keys"]);
    }

    /**
     * Search a secondary index
     *
     * @author Eric Stevens <estevens@taglabsinc.com>
     * @param string     $indexName - The name of the index to search
     * @param string     $indexType - The type of index ('int' or 'bin')
     * @param string|int $startOrExact
     * @param string|int optional   $end
     * @param            bool       optional $dedupe - whether to eliminate duplicate entries if any
     * @return array of Links
     */
    public function indexSearch($indexName, $indexType, $startOrExact, $end = null, $dedupe = false)
    {
        $url = Utils::buildIndexPath($this->client, $this, "{$indexName}_{$indexType}", $startOrExact, $end, null);
        $response = Utils::httpRequest('GET', $url);

        $obj = new Object($this->client, $this, null);

        $obj->populate($response, [200]);
        if (!$obj->exists()) {
            throw new Exception("Error searching index.");
        }
        $data = $obj->getData();
        $keys = array_map("urldecode", $data["keys"]);

        $seenKeys = [];
        foreach ($keys as $id => &$key) {
            if ($dedupe) {
                if (isset($seenKeys[$key])) {
                    unset($keys[$id]);
                    continue;
                }
                $seenKeys[$key] = true;
            }
            $key = new Link($this->name, $key);
            $key->client = $this->client;
        }

        return $keys;
    }

    /**
     * Check if a given key exists in a bucket
     *
     * @author Edgar Veiga <edgarmveiga@gmail.com>
     * @param string $key - The key to check
     * @return bool
     */
    public function hasKey($key)
    {
        $url = Utils::buildRestPath($this->client, $this, $key);
        $response = Utils::httpRequest('HEAD', $url);

        if ($response == null) {
            throw new Exception("Error checking if key exists.");
        }

        $status = $response[0]['http_code'];
        if ($status === 200) {
            return true;
        }

        return false;
    }
}

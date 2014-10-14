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

namespace Basho;

use Basho\Riak\MapReduce;

/**
 * Riak core class.
 *
 * This is the core class to this library which initializes and maintains your connection to the Riak instance.
 *
 * @package     Basho
 * @subpackage  Riak
 * @author      Riak Team and contributors <eng@basho.com> (https://github.com/basho/riak-php-client/contributors)
 * @copyright   2011-2014 Basho Technologies, Inc. and contributors.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 */
class Riak
{
    /**
     * Riak server cluster
     * @var array[string]
     */
    protected $hosts = [];

    /**
     * Configuration options for this client
     *
     * @var array
     */
    protected $config = [
        'port'          => 8098,
        'prefix'        => 'riak',
        'mapred_prefix' => 'mapred',
        'index_prefix'  => 'buckets',
    ];

    /**
     * Unique id for this client connection
     *
     * @var string
     */
    protected $clientId = '';

    /**
     * R-Value
     *
     * @var integer
     */
    protected $r = 2;

    /**
     * W-Value
     *
     * @var integer
     */
    protected $w = 2;

    /**
     * DW-Value
     *
     * @var integer
     */
    protected $dw = 2;

    /**
     * Construct a new Client object, defaults to port 8098.
     *
     * @param array $hosts  - array('127.0.0.1')
     * @param array $config - array('prefix', 'mapred_prefix', 'index_prefix')
     */
    public function __construct(array $hosts, array $config = [])
    {
        if (!empty($hosts['host'])) {
            $this->hosts = [$hosts];
        } else {
            $this->hosts = $hosts;
        }

        if (!empty($config)) {
            // use php array merge so passed in config overrides defaults
            $this->config = array_merge($this->config, $config);
        }

        $this->clientId = 'php_' . base_convert(mt_rand(), 10, 36);
    }

    /**
     * Get the R-value setting for this Client
     *
     * Default: 2
     *
     * @return integer
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * Set the R-value for this Client
     *
     * This value will be used
     * for any calls to get(...) or getBinary(...) where 1) no
     * R-value is specified in the method call and 2) no R-value has
     * been set in the Bucket.
     *
     * @param integer $r - The R value.
     * @return $this
     */
    public function setR($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get the W-value setting for this Client
     *
     * Default: 2
     *
     * @return integer
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set the W-value for this Client
     *
     * See setR(...) for a description of how these values are used.
     *
     * @param integer $w - The W value.
     * @return $this
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get the DW-value for this ClientOBject
     *
     * Default: 2
     *
     * @return integer
     */
    public function getDW()
    {
        return $this->dw;
    }

    /**
     * Set the DW-value for this Client
     *
     * See setR(...) for a description of how these values are used.
     *
     * @param  integer $dw - The DW value.
     * @return $this
     */
    public function setDW($dw)
    {
        $this->dw = $dw;

        return $this;
    }

    /**
     * Get the clientID for this Client.
     *
     * @return string
     */
    public function getClientID()
    {
        return $this->clientid;
    }

    /**
     * Set the clientID for this Client
     *
     * Should not be called unless you know what you are doing.
     *
     * @param string $clientID - The new clientID.
     * @return $this
     */
    public function setClientID($clientid)
    {
        $this->clientid = $clientid;

        return $this;
    }

    /**
     * Get all buckets
     *
     * @return array() of Bucket objects
     */
    public function buckets()
    {
        $url          = Utils::buildRestPath($this);
        $response     = Utils::httpRequest('GET', $url . '?buckets=true');
        $response_obj = json_decode($response[1]);
        $buckets      = [];
        foreach ($response_obj->buckets as $name) {
            $buckets[] = $this->bucket($name);
        }

        return $buckets;
    }

    /**
     * Get the bucket by the specified name
     *
     * Since buckets always exist, this will always return a Bucket.
     *
     * @return Bucket
     */
    public function bucket($name)
    {
        return new Bucket($this, $name);
    }

    /**
     * Check if the Riak server for this Client is alive
     *
     * @return boolean
     */
    public function isAlive()
    {
        $url = 'http://' . $this->host . ':' . $this->port . '/ping';
        $response = Utils::httpRequest('GET', $url);

        return ($response != null) && ($response[1] == 'OK');
    }


    # MAP/REDUCE/LINK FUNCTIONS

    /**
     * Start assembling a Map/Reduce operation
     *
     * @see MapReduce::add()
     * @return MapReduce
     */
    public function add($params)
    {
        $mr   = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array([&$mr, "add"], $args);
    }

    /**
     * Start assembling a Map/Reduce operation
     *
     * This command will return an error unless
     * executed against a Riak Search cluster.
     *
     * @see MapReduce::search()
     * @return MapReduce
     */
    public function search($params)
    {
        $mr   = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array([&$mr, "search"], $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     *
     * @see MapReduce::link()
     */
    public function link($params)
    {
        $mr   = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array([&$mr, "link"], $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     *
     * @see MapReduce::map()
     */
    public function map($params)
    {
        $mr   = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array([&$mr, "map"], $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     *
     * @see MapReduce::reduce()
     */
    public function reduce($params)
    {
        $mr   = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array([&$mr, "reduce"], $args);
    }
}

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

namespace Basho\Riak\Node;

/**
 * Class Config
 *
 * Configuration data structure object for connecting to a Riak node.
 *
 * @package     Basho\Riak\Node
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class Config
{
    /**
     * Host address
     *
     * @var string
     */
    protected $host = '';

    /**
     * Port number
     *
     * @var int
     */
    protected $port = 0;

    /**
     * User name
     *
     * @var string
     */
    protected $user = '';

    /**
     * User password
     *
     * @var string
     */
    protected $pass = '';

    /**
     * HTTP API flag
     *
     * If this is set to true, then the HTTP API for Riak will be used instead of Protocol Buffers
     *
     * @var bool
     */
    protected $http = false;

    /**
     * User authentication flag
     *
     * If this is set to true, then the client will attempt to authenticate to the Riak node
     *
     * @var bool
     */
    protected $auth = false;

    /**
     * @return boolean
     */
    public function isAuth()
    {
        return $this->auth;
    }

    /**
     * @param boolean $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return boolean
     */
    public function isHttp()
    {
        return $this->http;
    }

    /**
     * @param boolean $http
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
} 
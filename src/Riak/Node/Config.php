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

namespace Basho\Riak\Node;

/**
 * Configuration data structure object for connecting to a Riak node.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
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
     * Client / user authentication flag
     *
     * If true, client will use HTTPS (TLS1.2) to connect to Riak node
     *
     * @var bool
     */
    protected $auth = false;

    protected $ca_file = '';

    /**
     * [short description]
     *
     * @var string
     */
    protected $ca_directory = '';
    /**
     * Certificate to authenticate to Riak with
     *
     * @var string
     */
    protected $certificate = '';
    /**
     * Certificate to authenticate to Riak with
     *
     * @var string
     */
    protected $certificate_password = '';
    /**
     * [short description]
     *
     * @var string
     */
    protected $private_key = '';
    /**
     * [short description]
     *
     * @var string
     */
    protected $private_key_password = '';

    /**
     * @return string
     */
    public function getCaFile()
    {
        return $this->ca_file;
    }

    /**
     * @param string $ca_file
     */
    public function setCaFile($ca_file)
    {
        $this->ca_file = $ca_file;
    }

    /**
     * @return string
     */
    public function getCaDirectory()
    {
        return $this->ca_directory;
    }

    /**
     * @param string $ca_directory
     */
    public function setCaDirectory($ca_directory)
    {
        $this->ca_directory = $ca_directory;
    }

    /**
     * @return string
     */
    public function getCertificatePassword()
    {
        return $this->certificate_password;
    }

    /**
     * @param string $certificate_password
     */
    public function setCertificatePassword($certificate_password)
    {
        $this->certificate_password = $certificate_password;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->private_key;
    }

    /**
     * @param string $private_key
     */
    public function setPrivateKey($private_key)
    {
        $this->private_key = $private_key;
    }

    /**
     * @return string
     */
    public function getPrivateKeyPassword()
    {
        return $this->private_key_password;
    }

    /**
     * @param string $private_key_password
     */
    public function setPrivateKeyPassword($private_key_password)
    {
        $this->private_key_password = $private_key_password;
    }

    /**
     * @return string
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param string $certificate
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

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
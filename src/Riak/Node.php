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

use Basho\Riak\Node\Config;

/**
 * Class Node
 *
 * Contains the connection configuration to connect to a Riak node.
 *
 * @package     Basho\Riak
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class Node
{
    /**
     * Configuration
     *
     * Contains configuration needed to connect to a Riak node.
     *
     * @var Config|null
     */
    protected $config = null;

    /**
     * Inactive node
     *
     * This is only set to true if the node has been marked as unreachable.
     *
     * @var bool
     */
    protected $inactive = false;

    /**
     * Node signature
     *
     * This property is used to store a stateless unique identifier for this node.
     *
     * @var string
     */
    protected $signature = '';

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->setSignature();
    }

    /**
     * This should NEVER be invoked outside of this object.
     */
    private function setSignature()
    {
        $this->signature = md5($this->config);
    }

    /**
     * @return boolean
     */
    public function isInactive()
    {
        return $this->inactive;
    }

    /**
     * @param boolean $inactive
     */
    public function setInactive($inactive)
    {
        $this->inactive = $inactive;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->getConfig()->getHost();
    }

    /**
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->getConfig()->getPort();
    }

    public function useSsl()
    {
        return $this->getConfig()->isAuth();
    }

    public function execute(Command $command, Api $api)
    {
        // instantiate API connection

        // prepare request

        // send request

        // return object
    }
}
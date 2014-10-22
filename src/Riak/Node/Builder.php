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

use Basho\Riak\Node;
use Basho\Riak\Node\Builder\Exception;

/**
 * Class Node Builder
 *
 * This class follows the Builder design pattern and is the preferred method for creating Basho\Riak\Node objects for
 * connecting to your Riak node cluster.
 *
 * <code>
 * use Basho\Riak\Node\Builder as NodeBuilder;
 *
 * $nodes = (new NodeBuilder)
 *     ->withHost('127.0.0.1')
 *     ->buildLocalhost([10018, 10028, 10038, 10048, 10058]);
 * </code>
 *
 * @package     Basho\Riak\Node
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class Builder
{
    /**
     * Internal storage
     *
     * @var Config|null
     */
    protected $config = null;

    public function __construct()
    {
        $this->config = new Config();
    }

    /**
     * Build nodes with user / password authentication
     *
     * NOTICE: This is NOT IMPLEMENTED / SUPPORTED AT THIS TIME.
     *
     * User authentication and access rules are only available in Riak versions 2 and above. To use this feature, SSL
     * is required to communicate with your Riak nodes.
     *
     * @param $user
     * @param $pass
     * @return $this
     */
    public function withAuth($user, $pass)
    {
        $this->getConfig()->setUser($user);
        $this->getConfig()->setPass($pass);
        $this->getConfig()->setAuth(true);

        return $this;
    }

    /**
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Build distributed cluster
     *
     * Build node objects configured to listen on the same port but different hosts. Commonly used in
     * staging and production environments where you have multiple Riak nodes on multiple machines / vms.
     *
     * @param array $hosts
     * @return Node[]
     */
    public function buildCluster(array $hosts = ['127.0.0.1'])
    {
        $nodes = [];
        foreach ($hosts as $host) {
            $nodes[] = $this->withHost($host)->build();
        }

        return $nodes;
    }

    /**
     * Build node
     *
     * Validate configuration for a single node object, then build it
     *
     * @return Node
     */
    public function build()
    {
        $this->validate();

        return new Node($this->getConfig());
    }

    /**
     * Builder configuration validation
     *
     * Checks the current configuration of the Node Builder for errors. This method should be executed before each Node
     * is built.
     *
     * @throws Exception
     */
    protected function validate()
    {
        // verify we have a host address and port
        if (empty($this->host) || empty($this->port)) {
            throw new Exception('Node host address and port number are required.');
        }
        // TODO: Add validation for user authentication
    }

    /**
     * Build with host address
     *
     * Build node objects with configuration to use a specific host address
     *
     * @param $host
     * @return $this
     */
    public function withHost($host)
    {
        $this->getConfig()->setHost($host);

        return $this;
    }

    /**
     * Build local node cluster
     *
     * Build multiple node objects configured with the same host address but different ports. Commonly used in
     * development environments where you have multiple Riak nodes on a single machine / vm.
     *
     * @param array $ports
     * @return Node[]
     */
    public function buildLocalhost(array $ports = [8087])
    {
        $nodes = [];
        foreach ($ports as $port) {
            $nodes[] = $this->withPort($port)->build();
        }

        return $nodes;
    }

    /**
     * Build node objects with configuration to use a specific port number
     *
     * @param $port
     * @return $this
     */
    public function withPort($port)
    {
        $this->getConfig()->setPort($port);

        return $this;
    }
}
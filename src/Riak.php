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

namespace Basho;

use Basho\Riak\Api;
use Basho\Riak\Api\Http;
use Basho\Riak\Command;
use Basho\Riak\Exception;
use Basho\Riak\Node;

/**
 * This class maintains the list of nodes in the Riak cluster.
 *
 * <code>
 * $nodes = (new Node\Builder)
 *   ->atHost('localhost')
 *   ->onPort(8098)
 *   ->build()
 *
 * $riak = new Riak($nodes);
 *
 * $command = (new Command\Builder\FetchObject($riak))
 *   ->buildLocation('username', 'users')
 *   ->build();
 *
 * $response = $command->execute($command);
 *
 * $user = $response->getObject();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Riak
{
    /**
     * Riak server ring
     *
     * @var Node[]
     */
    protected $nodes = [];

    /**
     * Configuration options for this client
     *
     * @var array
     */
    protected $config = [
        'prefix'               => 'riak',
        'mapred_prefix'        => 'mapred',
        'index_prefix'         => 'buckets',
        'dns_server'           => '8.8.8.8',
        'max_connect_attempts' => 3,
    ];

    /**
     * The actively connected Riak Node from the ring
     *
     * @var int
     */
    protected $activeNodeIndex = 0;

    /**
     * API Bridge class to use
     *
     * @var Api|null
     */
    protected $api = NULL;

    /**
     * Construct a new Client object, defaults to port 8098.
     *
     * @param Node[] $nodes an array of Basho\Riak\Node objects
     * @param array $config a configuration object
     * @param Api $api
     *
     * @throws Exception
     */
    public function __construct(array $nodes, array $config = [], Api $api = NULL)
    {
        // wash any custom keys if any
        $this->nodes = array_values($nodes);
        $this->setActiveNodeIndex($this->pickNode());

        if (!empty($config)) {
            // use php array merge so passed in config overrides defaults
            $this->config = array_merge($this->config, $config);
        }

        if ($api) {
            $this->api = $api;
        } else {
            // default to HTTP bridge class
            $this->api = new Http($this->config);
        }
    }

    /**
     * Pick a random Node from the ring
     *
     * You can pick your friends, you can pick your node, but you can't pick your friend's node.  :)
     *
     * @param int $attempts
     * @return int
     * @throws Exception
     */
    protected function pickNode($attempts = 0)
    {
        $nodes       = $this->getNodes();
        $randomIndex = mt_rand(0, count($nodes) - 1);

        // check if node has been marked inactive
        if ($nodes[$randomIndex]->isInactive()) {
            // if we have not reached max connection attempts, use recursion
            if ($attempts < $this->getConfigValue('max_connect_attempts')) {
                return $this->pickNode($attempts + 1);
            } else {
                throw new Exception('Unable to connect to an active node.');
            }
        }

        return $randomIndex;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Get value from connection config
     *
     * @param $key
     *
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->config[$key];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Execute a Riak command
     *
     * @param Command $command
     *
     * @return Command\Response
     */
    public function execute(Command $command)
    {
        return $this->getActiveNode()->execute($command, $this->api);
    }

    /**
     * @return Node
     */
    public function getActiveNode()
    {
        $nodes = $this->getNodes();

        return $nodes[$this->getActiveNodeIndex()];
    }

    /**
     * @return int
     */
    public function getActiveNodeIndex()
    {
        return $this->activeNodeIndex;
    }

    /**
     * @param int $activeNodeIndex
     */
    public function setActiveNodeIndex($activeNodeIndex)
    {
        $this->activeNodeIndex = $activeNodeIndex;
    }

    /**
     * @return Api|null
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Accessor for the last request issued to the API. For debugging purposes.
     *
     * @return string
     */
    public function getLastRequest()
    {
        return $this->api->getRequest();
    }

    /**
     * Pick new active node
     *
     * Used when the currently active node fails to complete a command / query
     *
     * @return $this
     * @throws Exception
     */
    protected function pickNewNode()
    {
        // mark current active node as inactive
        $this->getActiveNode()->setInactive(true);
        $this->setActiveNodeIndex($this->pickNode());

        return $this;
    }
}
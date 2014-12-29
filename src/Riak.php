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

use Basho\Riak\Api\Http;
use Basho\Riak\Command;
use Basho\Riak\Exception;
use Basho\Riak\Node;

/**
 * Class Riak
 *
 * This class is the quarterback of the Riak PHP client library. It maintains the list of nodes in the Riak cluster,
 *
 *
 * @package     Basho\Riak
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
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
     * Unique id for this client connection
     *
     * @var string
     */
    protected $clientId = '';

    /**
     * The actively connected Riak Node from the ring
     *
     * @var int
     */
    protected $activeNodeIndex = 0;

    /**
     * Construct a new Client object, defaults to port 8098.
     *
     * @param Node[] $nodes  an array of Basho\Riak\Node objects
     * @param array  $config a configuration object
     */
    public function __construct(array $nodes, array $config = [])
    {
        $this->clientId = 'php_' . base_convert(mt_rand(), 10, 36);

        // wash any custom keys if any
        $this->nodes    = array_values($nodes);
        $this->setActiveNodeIndex($this->pickNode());

        if (!empty($config)) {
            // use php array merge so passed in config overrides defaults
            $this->config = array_merge($this->config, $config);
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
     * @return null
     */
    public function execute(Command $command)
    {
        $result = null;

        $result = $this->getActiveNode()->execute($command, $this->getApi());

        return $result;
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

    public function getApi()
    {
        return new Http($this->getClientID());
    }

    /**
     * @return string
     */
    public function getClientID()
    {
        return $this->clientId;
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
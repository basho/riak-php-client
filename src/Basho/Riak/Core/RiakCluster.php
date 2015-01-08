<?php

namespace Basho\Riak\Core;

use Basho\Riak\RiakConfig;
use Basho\Riak\RiakException;
use Basho\Riak\Core\RiakOperation;

/**
 * This class represents a Riak Cluster upon which operations are executed.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakCluster
{
    /**
     * @var array
     */
    private $nodes;

    /**
     * @var \Basho\Riak\RiakConfig
     */
    private $config;

    /**
     * @param \Basho\Riak\RiakConfig $config
     */
    public function __construct(RiakConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Adds a RiakNode to this cluster.
     *
     * @param \Basho\Riak\Core\RiakNode $node
     */
    public function addNode(RiakNode $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * Removes the provided node from the cluster.
     *
     * @param RiakNode $node
     *
     * @return \Basho\Riak\Core\RiakNode|null
     */
    public function removeNode(RiakNode $node)
    {
        $index  = array_search($node, $this->nodes);
        $result = isset($this->nodes[$index]) ? $this->nodes[$index] : null;

        if ($result === null) {
            return;
        }

        unset($this->nodes[$index]);

        return $result;
    }

    /**
     * Returns a list of nodes in this cluster
     *
     * @return \Basho\Riak\Core\RiakNode[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Set a a list of nodes in this cluster
     *
     * @param array $nodes
     */
    public function setNodes(array $nodes)
    {
        array_walk($nodes, [$this, 'addNode']);
    }

    /**
     * @return \Basho\Riak\RiakConfig
     */
    public function getRiakConfig()
    {
        return $this->config;
    }

    /**
     * Pick a node and execute the command in this cluster
     *
     * @param \Basho\Riak\Core\RiakOperation $operation
     *
     * @return \Basho\Riak\RiakResponse
     */
    public function execute(RiakOperation $operation)
    {
        if (empty($this->nodes)) {
            throw new RiakException('There are no nodes in the cluster.');
        }

        $size  = count($this->nodes);
        $index = mt_rand(0, $size - 1);
        $node  = $this->nodes[$index];

        return $node->execute($operation);
    }
}

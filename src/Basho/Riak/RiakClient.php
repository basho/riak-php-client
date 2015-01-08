<?php

namespace Basho\Riak;

use Basho\Riak\Core\RiakCluster;
use Basho\Riak\RiakConfig;

/**
 * The client used to perform operations on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakClient
{
    /**
     * @var \Basho\Riak\Core\RiakCluster
     */
    private $cluster;

    /**
     * @var \Basho\Riak\RiakConfig
     */
    private $config;

    /**
     * @param \Basho\Riak\RiakConfig       $config
     * @param \Basho\Riak\Core\RiakCluster $cluster
     */
    public function __construct(RiakConfig $config, RiakCluster $cluster)
    {
        $this->config  = $config;
        $this->cluster = $cluster;
    }

    /**
     * @return \Basho\Riak\Core\RiakCluster
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @return \Basho\Riak\RiakConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Execute a RiakCommand.
     *
     * Calling this method causes the client to execute the provided RiakCommand.
     *
     * @param \Basho\Riak\RiakCommand $command
     *
     * @return \Basho\Riak\RiakResponse
     */
    public function execute(RiakCommand $command)
    {
        return $command->execute($this->cluster);
    }
}

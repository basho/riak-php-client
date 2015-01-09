<?php

namespace Basho\Riak\Command\Kv;

use Basho\Riak\Cap\VClock;
use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Operation\Kv\DeleteOperation;
use Basho\Riak\Command\Kv\Builder\DeleteValueBuilder;

/**
 * Command used to delete a value from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class DeleteValue implements RiakCommand
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var \Basho\Riak\Cap\VClock
     */
    private $vClock;

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     * @param array                               $options
     */
    public function __construct(RiakLocation $location, $options)
    {
        $this->location = $location;
        $this->options  = $options;
    }

    /**
     * @param \Basho\Riak\Cap\VClock $vClock
     *
     * @return \Basho\Riak\Command\DeleteValue
     */
    public function withVClock(VClock $vClock)
    {
        $this->vClock = $vClock;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $factory   = $config->getConverterFactory();
        $converter = $config->getRiakObjectConverter();
        $operation = new DeleteOperation($factory, $converter, $this->location, $this->options, $this->vClock);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     *
     * @return \Basho\Riak\Command\Kv\Builder\DeleteValueBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new DeleteValueBuilder($location);
    }
}

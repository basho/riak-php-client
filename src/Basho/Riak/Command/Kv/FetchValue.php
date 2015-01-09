<?php

namespace Basho\Riak\Command\Kv;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Operation\Kv\FetchOperation;
use Basho\Riak\Command\Kv\Builder\FetchValueBuilder;

/**
 * Command used to fetch a value from Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchValue implements RiakCommand
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
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     * @param array                               $options
     */
    public function __construct(RiakLocation $location, $options)
    {
        $this->location = $location;
        $this->options  = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $factory   = $config->getConverterFactory();
        $converter = $config->getRiakObjectConverter();
        $operation = new FetchOperation($factory, $converter, $this->location, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     *
     * @return \Basho\Riak\Command\Kv\Builder\FetchValueBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new FetchValueBuilder($location);
    }
}

<?php

namespace Basho\Riak\Command\Kv;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Operation\StoreOperation;
use Basho\Riak\Command\Kv\Builder\StoreValueBuilder;

/**
 * Command used to store a value in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValue implements RiakCommand
{
    /**
     * @var \Basho\Riak\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation       $location
     * @param \Basho\Riak\Core\Query\RiakObject|mixed   $value
     * @param array                                     $options
     */
    public function __construct(RiakLocation $location, $value = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->value    = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $factory   = $config->getConverterFactory();
        $converter = $config->getRiakObjectConverter();
        $operation = new StoreOperation($factory, $converter, $this->location, $this->value, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param \Basho\Riak\Core\Query\RiakObject|mixed $value
     *
     * @return \Basho\Riak\Command\Kv\Builder\StoreValueBuilder
     */
    public static function builder(RiakLocation $location = null, $value = null)
    {
        return new StoreValueBuilder($location, $value);
    }
}
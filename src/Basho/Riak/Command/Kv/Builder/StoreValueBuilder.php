<?php

namespace Basho\Riak\Command\Kv\Builder;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Command\Kv\StoreValue;

/**
 * Used to construct a StoreValue command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValueBuilder extends Builder
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation     $location
     * @param \Basho\Riak\Core\Query\RiakObject|mixed $value
     */
    public function __construct(RiakLocation $location = null, $value = null)
    {
        $this->location = $location;
        $this->value    = $value;
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     *
     * @return \Basho\Riak\Command\Kv\Builder\FetchValueBuilder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakObject|mixed $value
     *
     * @return \Basho\Riak\Command\Kv\Builder\FetchValueBuilder
     */
    public function withValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Build a FetchValue object
     *
     * @return \Basho\Riak\Command\Kv\StoreValue
     */
    public function build()
    {
        return new StoreValue($this->location, $this->value, $this->options);
    }
}

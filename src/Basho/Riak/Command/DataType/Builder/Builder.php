<?php

namespace Basho\Riak\Command\DataType\Builder;

/**
 * Used to construct a command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     */
    public function __construct(RiakLocation $location = null)
    {
        $this->location = $location;
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
     * Add an optional setting for this command.
     * This will be passed along with the request to Riak.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return \Basho\Riak\Command\DataType\Builder
     */
    public function withOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Build a riak command object
     *
     * @return \Basho\Riak\RiakCommand
     */
    abstract public function build();
}
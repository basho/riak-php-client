<?php

namespace Basho\Riak\Command\Kv\Builder;

/**
 * Used to construct a command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class Builder
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Add an optional setting for this command.
     * This will be passed along with the request to Riak.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return \Basho\Riak\Command\Builder\Builder
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

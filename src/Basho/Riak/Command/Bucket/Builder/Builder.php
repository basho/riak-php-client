<?php

namespace Basho\Riak\Command\Bucket\Builder;

/**
 * Used to construct a command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * Build a riak command object
     *
     * @return \Basho\Riak\RiakCommand
     */
    abstract public function build();
}

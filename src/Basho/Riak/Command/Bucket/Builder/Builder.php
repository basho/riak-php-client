<?php

namespace Basho\Riak\Command\Bucket\Builder;

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
     * Build a riak command object
     *
     * @return \Basho\Riak\RiakCommand
     */
    abstract public function build();
}

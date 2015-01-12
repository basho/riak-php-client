<?php

namespace Basho\Riak\Core\Message;

/**
 * Base class for all requests.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class Request
{
    /**
     * Error handler for unknown property mutator.
     *
     * @param string $name  Unknown property name.
     * @param mixed  $value Property value.
     *
     * @throws \BadMethodCallException
     */
    public function __set($name, $value)
    {
        throw new \InvalidArgumentException(
            sprintf("Unknown property '%s' on '%s'.", $name, get_class($this))
        );
    }
}

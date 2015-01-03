<?php

namespace Basho\Riak\Core\Message;

/**
 * Base class for all requests.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Request
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
        throw new \BadMethodCallException(
            sprintf("Unknown property '%s' on '%s'.", $name, get_class($this))
        );
    }
}

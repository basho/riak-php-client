<?php

namespace Basho\Riak\Core\Query;

/**
 * Encapsulates a key and Namespace.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakLocation
{
    /**
     * @var \Basho\Riak\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var string
     */
    private $key;

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     * @param string                               $key
     */
    public function __construct(RiakNamespace $namespace, $key)
    {
        $this->namespace = $namespace;
        $this->key       = (string) $key;
    }

    /**
     * Returns the key for this location.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Return the Namespace for this location.
     *
     * @return \Basho\Riak\Core\Query\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     */
    public function setNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;
    }
}

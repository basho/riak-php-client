<?php

namespace Basho\Riak\Core\Query\Crdt;

/**
 * Representation of the Riak map datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakMap implements DataType
{
    /**
     * @var array
     */
    private $value;

    /**
     * @param array $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->value[$name])
            ? $this->value[$name]
            : null;
    }
}

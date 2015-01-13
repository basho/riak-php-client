<?php

namespace Basho\Riak\Core\Query\Crdt;

/**
 * Representation of the Riak flag datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakFlag implements DataType
{
    /**
     * @var boolean
     */
    private $value;

    /**
     * @param boolean $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function getValue()
    {
        return $this->value;
    }
}

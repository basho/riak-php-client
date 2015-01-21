<?php

namespace Basho\Riak\Core\Query\Crdt;

/**
 * Representation of the Riak counter datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakCounter implements DataType
{
    /**
     * @var integer
     */
    private $value;

    /**
     * @param integer $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }
}

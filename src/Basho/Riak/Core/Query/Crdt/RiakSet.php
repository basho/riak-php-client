<?php

namespace Basho\Riak\Core\Query\Crdt;

/**
 * Representation of the Riak set datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakSet implements DataType
{
    /**
     * @var array
     */
    private $elements;

    /**
     * @param array $elements
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->elements;
    }
}

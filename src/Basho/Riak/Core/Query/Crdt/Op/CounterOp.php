<?php

namespace Basho\Riak\Core\Query\Crdt\Op;

/**
 * Riak Counter crdt op.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class CounterOp implements CrdtOp
{
    /**
     * @var integer
     */
    private $increment;

    /**
     * @param integer $increment
     */
    public function __construct($increment)
    {
        $this->increment = $increment;
    }

    /**
     * @return integer
     */
    public function getIncrement()
    {
        return $this->increment;
    }
}

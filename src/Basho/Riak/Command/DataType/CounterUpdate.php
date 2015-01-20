<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\Core\Query\Crdt\Op\CounterOp;

/**
 * An update to a Riak counter datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class CounterUpdate implements DataTypeUpdate
{
    /**
     * @var integer
     */
    private $delta;

    /**
     * @param integer $delta
     */
    public function __construct($delta = 0)
    {
        $this->delta = $delta;
    }

    /**
     * @param integer $delta
     *
     * @return \Basho\Riak\Command\DataType\CounterUpdate
     */
    public function withDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        return new CounterOp($this->delta);
    }
}

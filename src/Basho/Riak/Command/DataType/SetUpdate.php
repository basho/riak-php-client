<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\Core\Query\Crdt\Op\SetOp;

/**
 * An update to a Riak set datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class SetUpdate implements DataTypeUpdate
{
    /**
     * @var array
     */
    private $adds = [];

    /**
     * @var array
     */
    private $removes = [];

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\SetUpdate
     */
    public function add($value)
    {
        $this->adds[] = $value;

        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Basho\Riak\Command\DataType\SetUpdate
     */
    public function remove($value)
    {
        $this->removes[] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        return new SetOp($this->adds, $this->removes);
    }

    /**
     * @return \Basho\Riak\Command\DataType\SetUpdate
     */
    static public function create()
    {
        return new SetUpdate();
    }
}

<?php

namespace Basho\Riak\Core\Query\Crdt\Op;

/**
 * Riak Set crdt op.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class SetOp implements CrdtOp
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
     * @param array $adds
     * @param array $removes
     */
    public function __construct(array $adds, array $removes)
    {
        $this->adds    = $adds;
        $this->removes = $removes;
    }

    /**
     * @return array
     */
    public function getAdds()
    {
        return $this->adds;
    }

    /**
     * @return array
     */
    public function getRemoves()
    {
        return $this->removes;
    }
}

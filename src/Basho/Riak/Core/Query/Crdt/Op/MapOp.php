<?php

namespace Basho\Riak\Core\Query\Crdt\Op;

/**
 * Riak Map crdt op.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class MapOp implements CrdtOp
{
    /**
     * @var array
     */
    private $removes;

    /**
     * @var array
     */
    private $updates;

    /**
     * @param array $updates
     * @param array $removes
     */
    public function __construct(array $updates, array $removes)
    {
        $this->updates = $updates;
        $this->removes = $removes;
    }

    /**
     * @return array
     */
    public function getRemoves()
    {
        return $this->removes;
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        return $this->updates;
    }
}

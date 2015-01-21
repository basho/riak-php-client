<?php

namespace Basho\Riak\Command\DataType;

/**
 * An object that represents an update to a Riak datatype.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
interface DataTypeUpdate
{
    /**
     * @return \Basho\Riak\Core\Query\Crdt\Op\CrdtOp
     */
    public function getOp();
}

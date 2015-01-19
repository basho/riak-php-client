<?php

namespace Basho\Riak\Core\Adapter\Rpb;

use Basho\Riak\ProtoBuf;
use Basho\Riak\RiakException;
use Basho\Riak\Core\Adapter\Strategy;
use Basho\Riak\Core\Query\Crdt\Op\SetOp;
use Basho\Riak\Core\Query\Crdt\Op\CrdtOp;
use Basho\Riak\Core\Adapter\Rpb\RpbClient;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;

/**
 * Base rpb strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class RpbStrategy implements Strategy
{
    /**
     * @var \Basho\Riak\Core\Adapter\Rpb\RpbClient
     */
    protected $client;

    /**
     * @param \Basho\Riak\Core\Adapter\Rpb\RpbClient $client
     */
    public function __construct(RpbClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return \Basho\Riak\ProtoBuf\DtOp
     */
    protected function createCrdtOp(CrdtOp $op)
    {
        $crdtOp = new ProtoBuf\DtOp();

        if ($op instanceof CounterOp) {
            $counterOp = new ProtoBuf\CounterOp();
            $increment = $op->getIncrement();

            $counterOp->setIncrement($increment);
            $crdtOp->setCounterOp($counterOp);

            return $crdtOp;
        }

        if ($op instanceof SetOp) {
            $setOp = new ProtoBuf\SetOp();

            $setOp->setRemoves($op->getRemoves());
            $setOp->setAdds($op->getAdds());
            $crdtOp->setSetOp($setOp);

            return $crdtOp;
        }

        throw new RiakException(sprintf('Unknown crdt op : %s', get_class($op)));
    }
}

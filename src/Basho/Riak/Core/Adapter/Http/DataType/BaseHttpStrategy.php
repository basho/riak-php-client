<?php

namespace Basho\Riak\Core\Adapter\Http\DataType;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Adapter\Strategy;
use Basho\Riak\Core\Query\Crdt\Op\CrdtOp;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;
use Basho\Riak\Core\Query\Crdt\Op\SetOp;

/**
 * Base http strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseHttpStrategy implements Strategy
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $validResponseCodes = [];

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return string
     */
    protected function buildPath($type, $bucket, $key)
    {
        return sprintf('/types/%s/buckets/%s/datatypes/%s', $type, $bucket, $key);
    }

    /**
     * @param string $method
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $type, $bucket, $key)
    {
        $path    = $this->buildPath($type, $bucket, $key);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return string
     */
    protected function createCrdtOpBody(CrdtOp $op)
    {
        $map  = $this->createCrdtOpMap($op);
        $json = json_encode($map);

        return $json;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return string
     */
    protected function createCrdtOpMap(CrdtOp $op)
    {
        if ($op instanceof CounterOp) {
            return $op->getIncrement();
        }

        if ($op instanceof SetOp) {
            $map    = [];
            $add    = $op->getAdds();
            $remove = $op->getRemoves();

            if ( ! empty($add)) {
                $map['add_all'] = $add;
            }

            if ( ! empty($remove)) {
                $map['remove'] = $remove;
            }

            return $map;
        }

        throw new RiakException(sprintf('Unknown crdt op : %s', get_class($op)));
    }
}

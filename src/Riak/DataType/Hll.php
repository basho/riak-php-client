<?php

namespace Basho\Riak\DataType;

use Basho\Riak\DataType;

/**
 * Class Hll
 *
 * Data structure for HyperLogLog crdt
 *
 * @author Luke Bakken <lbakken@basho.com>
 */
class Hll extends DataType
{
    /**
     * {@inheritdoc}
     */
    const TYPE = 'hll';

    /**
     * @var string
     */
    private $context;

    /**
     * @param array $data
     * @param $context
     */
    public function __construct(array $data, $context)
    {
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * @return integer
     */
    public function getCardinality()
    {
        return $this->data;
    }

    public function getContext()
    {
        return $this->context;
    }
}

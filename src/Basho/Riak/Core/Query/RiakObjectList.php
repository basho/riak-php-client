<?php

namespace Basho\Riak\Core\Query;

use BadMethodCallException;
use OutOfBoundsException;
use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;
use Countable;

/**
 * Represents list of riak objects.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakObjectList implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var \Basho\Riak\Core\Query\RiakObject[]
     */
    private $list;

    /**
     * @param \Basho\Riak\Core\Query\RiakObject[] $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    public function first()
    {
        return reset($this->list) ?: null;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->list);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->list);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ( ! isset($this->list[$offset])) {
            throw new OutOfBoundsException("Undefined key : $offset");
        }

        return $this->list[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException();
    }
}

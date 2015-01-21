<?php

namespace Basho\Riak\Core\Query\Index;

use Basho\Riak\Core\Query\RiakList;

/**
 * Represents list of riak indexes.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakIndexList extends RiakList
{
    /**
     * @param \Basho\Riak\Core\Query\Index\RiakIndex[] $list
     */
    public function __construct(array $list = [])
    {
        parent::__construct([]);

        array_walk($list, [$this, 'addIndex']);
    }

    /**
     * @param \Basho\Riak\Core\Query\Index\RiakIndex $index
     */
    public function addIndex(RiakIndex $index)
    {
        $this->list[$index->getName()] = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->addIndex($value);
    }

    /**
     * @return array
     */
    public function toFullNameArray()
    {
        $values = [];

        foreach ($this->list as $index) {
            $values[$index->getFullName()] = $index->getValues();
        }

        return $values;
    }
}

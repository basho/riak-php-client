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
     * @return array
     */
    public function toArray()
    {
        $values = [];

        foreach ($this->list as $index) {
            $values[$index->getFullName()] = $index->getValues();
        }

        return $values;
    }
}

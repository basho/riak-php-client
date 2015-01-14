<?php

namespace Basho\Riak\Core\Query\Index;

/**
 * Implementation used to access a Riak _int indexes
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class RiakIndexInt extends RiakIndex
{
    /**
     * {@inheritdoc}
     */
    public function addValue($value)
    {
        $this->values[] = (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'int';
    }
}

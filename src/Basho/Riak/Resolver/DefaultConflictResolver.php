<?php

namespace Basho\Riak\Resolver;

use Basho\Riak\Core\Query\RiakObjectList;

/**
 * A conflict resolver that doesn't resolve conflict
 * If it is presented with a collection of siblings with more than one entry it throws an Exception
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class DefaultConflictResolver implements ConflictResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(RiakObjectList $siblings)
    {
        if (count($siblings) == 1) {
            return $siblings->first();
        }

        if ($siblings->isEmpty()) {
            return;
        }

        throw new UnresolvedConflictException();
    }
}

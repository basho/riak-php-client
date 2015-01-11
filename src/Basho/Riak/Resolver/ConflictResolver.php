<?php

namespace Basho\Riak\Resolver;

use Basho\Riak\Core\Query\RiakObjectList;

/**
 * Interface used to resolve siblings.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
interface ConflictResolver
{
    /**
     * Resolve a set a of siblings to a single object.
     *
     * @param \Basho\Riak\Core\Query\RiakObjectList $siblings
     *
     * @return \Basho\Riak\Core\Query\RiakObject
     *
     * @throws \Basho\Riak\Resolver\UnresolvedConflictException
     */
    public function resolve(RiakObjectList $siblings);
}

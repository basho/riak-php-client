<?php

namespace Basho\Riak\Resolver;

/**
 * Simple factory for ConflictResolver objects.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class ResolverFactory
{
    /**
     * @var \Basho\Riak\Resolver\ConflictResolver[]
     */
    private $resolvers;

    /**
     * @var \Basho\Riak\Resolver\DefaultConflictResolver
     */
    private $default;

    /**
     * Initialize the default resolver
     */
    public function __construct()
    {
        $this->default = new DefaultConflictResolver();
    }

    /**
     * @return \Basho\Riak\Resolver\ConflictResolver[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * @param string $class
     *
     * @return \Basho\Riak\Resolver\ConflictResolver[]
     */
    public function getResolver($class)
    {
        if (isset($this->resolvers[$class])) {
            return $this->resolvers[$class];
        }

        return $this->default;
    }

    /**
     * @param string                                 $type
     * @param \Basho\Riak\Resolver\ConflictResolver  $resolver
     */
    public function addResolver($type, ConflictResolver $resolver)
    {
        $this->resolvers[$type] = $resolver;
    }
}

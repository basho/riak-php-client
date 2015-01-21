<?php

namespace Basho\Riak\Command\Bucket\Builder;

use Basho\Riak\Command\Bucket\StoreBucketProperties;
use Basho\Riak\Core\Query\RiakNamespace;

/**
 * Used to construct a StoreBucketProperties command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreBucketPropertiesBuilder extends Builder
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $namespace;

    /**
     * @var array
     */
    private $properties;

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     * @param array                                $properties
     */
    public function __construct(RiakNamespace $namespace = null, array $properties = [])
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     *
     * @return \Basho\Riak\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Add an propertu setting for this command.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Basho\Riak\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreBucketProperties
     */
    public function build()
    {
        return new StoreBucketProperties($this->namespace, $this->properties);
    }
}

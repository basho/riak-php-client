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
     * @param \Basho\Riak\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace = null)
    {
        $this->namespace = $namespace;
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
     * Build a command object
     *
     * @return \Basho\Riak\Command\DataType\StoreBucketProperties
     */
    public function build()
    {
        return new StoreBucketProperties($this->namespace);
    }
}
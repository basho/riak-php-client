<?php

namespace Basho\Riak\Command\Bucket\Builder;

use Basho\Riak\Command\Bucket\FetchBucketProperties;
use Basho\Riak\Core\Query\RiakNamespace;

/**
 * Used to construct a FetchBucketProperties command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchBucketPropertiesBuilder extends Builder
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
     * @return \Basho\Riak\Command\Bucket\FetchBucketProperties
     */
    public function build()
    {
        return new FetchBucketProperties($this->namespace);
    }
}

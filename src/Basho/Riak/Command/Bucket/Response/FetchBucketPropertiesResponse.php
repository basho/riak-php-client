<?php

namespace Basho\Riak\Command\Bucket\Response;

use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Query\BucketProperties;

/**
 * Fetch Bucket Properties Response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchBucketPropertiesResponse extends Response
{
    /**
     * @var \Basho\Riak\Core\Query\BucketProperties
     */
    private $properties;

    /**
     * @param \Basho\Riak\Command\Bucket\Response\RiakNamespace $namespace
     * @param \Basho\Riak\Core\Query\BucketProperties           $properties
     */
    public function __construct(RiakNamespace $namespace, BucketProperties $properties)
    {
        parent::__construct($namespace);

        $this->properties = $properties;
    }

    /**
     * @return \Basho\Riak\Core\Query\BucketProperties
     */
    public function getProperties()
    {
        return $this->properties;
    }
}

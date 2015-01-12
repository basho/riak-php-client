<?php

namespace Basho\Riak\Command\Bucket\Response;

use Basho\Riak\RiakResponse;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Query\BucketProperties;

/**
 * Base Bucket Response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Basho\Riak\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Basho\Riak\Command\Bucket\Response\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace)
    {
        $this->namespace  = $namespace;
    }

    /**
     * @return \Basho\Riak\Core\Query\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}

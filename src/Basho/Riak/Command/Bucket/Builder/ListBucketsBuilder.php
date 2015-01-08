<?php

namespace Basho\Riak\Command\Bucket\Builder;

use Basho\Riak\Command\Bucket\ListBuckets;
use Basho\Riak\Core\Query\RiakNamespace;

/**
 * Used to construct a ListBuckets command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class ListBucketsBuilder extends Builder
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $type
     */
    public function __construct(RiakNamespace $type = null)
    {
        $this->type = $type;
    }

    /**
     * @param string $type
     *
     * @return \Basho\Riak\Command\Bucket\Builder\ListBucketsBuilder
     */
    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Add an optional setting for this command.
     * This will be passed along with the request to Riak.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return \Basho\Riak\Command\Bucket\Builder\ListBucketsBuilder
     */
    public function withOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Basho\Riak\Command\Bucket\FetchBucketProperties
     */
    public function build()
    {
        return new ListBuckets($this->type, $this->options);
    }
}

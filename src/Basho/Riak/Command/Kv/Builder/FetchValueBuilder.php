<?php

namespace Basho\Riak\Command\Kv\Builder;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Command\Kv\FetchValue;

/**
 * Used to construct a FetchValue command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class FetchValueBuilder extends Builder
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     */
    public function __construct(RiakLocation $location = null)
    {
        $this->location = $location;
    }

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation $location
     *
     * @return \Basho\Riak\Command\Kv\Builder\FetchValueBuilder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Build a FetchValue object
     *
     * @return \Basho\Riak\Command\Kv\FetchValue
     */
    public function build()
    {
        return new FetchValue($this->location, $this->options);
    }
}

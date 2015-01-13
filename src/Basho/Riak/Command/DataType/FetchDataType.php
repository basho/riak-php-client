<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\RiakCluster;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Command\DataType\Builder\FetchCounterBuilder;
use Basho\Riak\Core\Operation\DataType\FetchCounterOperation;

/**
 * Command used to fetch a datatype from Riak.
 *
 * @author    Christopher Mancini <cmancini@basho.com>
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class FetchDataType implements RiakCommand
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    protected $location;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param \Basho\Riak\Command\Kv\RiakLocation $location
     * @param array                               $options
     */
    public function __construct(RiakLocation $location, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
    }
}

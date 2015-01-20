<?php

namespace Basho\Riak\Command\DataType;

use Basho\Riak\RiakCommand;
use Basho\Riak\Core\Query\RiakLocation;

/**
 * Command used to update or create a counter datatype in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class StoreDataType implements RiakCommand
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
     * @var \Basho\Riak\Command\DataType\DataTypeUpdate
     */
    protected $update;

    /**
     * @param \Basho\Riak\Core\Query\RiakLocation         $location
     * @param \Basho\Riak\Command\DataType\DataTypeUpdate $update
     * @param array                                       $options
     */
    public function __construct(RiakLocation $location, DataTypeUpdate $update, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->update   = $update;
    }
}

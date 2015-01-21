<?php

namespace Basho\Riak\Command\Kv\Builder;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Command\Kv\DeleteValue;
use Basho\Riak\Cap\VClock;

/**
 * Used to construct a DeleteValue command.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class DeleteValueBuilder extends Builder
{
    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Cap\VClock
     */
    private $vClock;

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
     * @return \Basho\Riak\Command\Kv\Builder\DeleteValueBuilder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param \Basho\Riak\Cap\VClock $vClock
     *
     * @return \Basho\Riak\Command\DeleteValue
     */
    public function withVClock(VClock $vClock)
    {
        $this->vClock = $vClock;

        return $this;
    }

    /**
     * Build a DeleteValue object
     *
     * @return \Basho\Riak\Command\Kv\DeleteValue
     */
    public function build()
    {
        $command = new DeleteValue($this->location, $this->options);

        if ($this->vClock !== null) {
            $command->withVClock($this->vClock);
        }

        return $command;
    }
}

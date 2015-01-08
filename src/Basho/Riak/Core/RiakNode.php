<?php

namespace Basho\Riak\Core;

use Basho\Riak\Core\RiakOperation;

/**
 * This class represents a Riak Node.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakNode
{
    /**
     * @var \Basho\Riak\Core\RiakAdapter
     */
    private $adapter;

    /**
     * @return \Basho\Riak\Core\RiakAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param \Basho\Riak\Core\RiakAdapter $adapter
     */
    public function __construct(RiakAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param \Basho\Riak\Core\RiakOperation $operation
     *
     * @return \Basho\Riak\RiakResponse
     */
    public function execute(RiakOperation $operation)
    {
        return $operation->execute($this->adapter);
    }
}

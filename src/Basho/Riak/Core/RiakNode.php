<?php

namespace Basho\Riak\Core;


/**
 * This class represents a Riak Node.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
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

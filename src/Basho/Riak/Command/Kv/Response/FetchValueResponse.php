<?php

namespace Basho\Riak\Command\Kv\Response;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Converter\ConverterFactory;

/**
 * Fetch Value Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchValueResponse extends Response
{
    /**
     * @var boolean
     */
    private $notFound;

    /**
     * @var boolean
     */
    private $unchanged;

    /**
     * @param \Basho\Riak\Core\Converter\ConverterFactory $converterFactory
     * @param \Basho\Riak\Core\Query\RiakLocation         $location
     * @param array                                       $values
     */
    public function __construct(ConverterFactory $converterFactory, RiakLocation $location, array $values)
    {
        parent::__construct($converterFactory, $location, $values);
    }

    /**
     * @return boolean
     */
    public function getNotFound()
    {
        return $this->notFound;
    }

    /**
     * @return boolean
     */
    public function getUnchanged()
    {
        return $this->unchanged;
    }

    /**
     * @param boolean $notFound
     */
    public function setNotFound($notFound)
    {
        $this->notFound = $notFound;
    }

    /**
     * @param boolean $unchanged
     */
    public function setUnchanged($unchanged)
    {
        $this->unchanged = $unchanged;
    }
}

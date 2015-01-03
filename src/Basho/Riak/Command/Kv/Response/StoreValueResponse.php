<?php

namespace Basho\Riak\Command\Kv\Response;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Converter\ConverterFactory;

/**
 * Store Value Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValueResponse extends Response
{
    /**
     * @var string
     */
    private $generatedKey;

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
     * @return string
     */
    public function getGeneratedKey()
    {
        return $this->unchanged;
    }

    /**
     * @param string $generatedKey
     */
    public function setGeneratedKey($generatedKey)
    {
        $this->generatedKey = $generatedKey;
    }
}

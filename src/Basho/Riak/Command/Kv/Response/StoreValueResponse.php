<?php

namespace Basho\Riak\Command\Kv\Response;

use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakObjectList;
use Basho\Riak\Converter\ConverterFactory;

/**
 * Store Value Response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class StoreValueResponse extends Response
{
    /**
     * @var string
     */
    private $generatedKey;

    /**
     * @param \Basho\Riak\Converter\ConverterFactory $converterFactory
     * @param \Basho\Riak\Core\Query\RiakLocation    $location
     * @param \Basho\Riak\Core\Query\RiakObjectList  $values
     */
    public function __construct(ConverterFactory $converterFactory, RiakLocation $location, RiakObjectList $values)
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

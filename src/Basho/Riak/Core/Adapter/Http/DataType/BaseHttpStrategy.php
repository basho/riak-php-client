<?php

namespace Basho\Riak\Core\Adapter\Http\DataType;

use GuzzleHttp\ClientInterface;
use Basho\Riak\Core\Adapter\Http\HttpStrategy;

/**
 * Base http strategy.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class BaseHttpStrategy extends HttpStrategy
{
    /**
     * @var \Basho\Riak\Core\Adapter\Http\DataType\CrdtOpConverter
     */
    protected $opConverter;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);

        $this->opConverter = new CrdtOpConverter();
    }

    /**
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return string
     */
    protected function buildPath($type, $bucket, $key)
    {
        return sprintf('/types/%s/buckets/%s/datatypes/%s', $type, $bucket, $key);
    }

    /**
     * @param string $method
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $type, $bucket, $key)
    {
        $path    = $this->buildPath($type, $bucket, $key);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}

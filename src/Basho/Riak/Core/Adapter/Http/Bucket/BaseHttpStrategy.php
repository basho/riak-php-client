<?php

namespace Basho\Riak\Core\Adapter\Http\Bucket;

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
     * @param string $type
     * @param string $bucket
     *
     * @return string
     */
    protected function buildPath($type, $bucket)
    {
        if ($type === null) {
            return sprintf('/buckets/%s/props', $bucket);
        }

        return sprintf('/types/%s/buckets/%s/props', $type, $bucket);
    }

    /**
     * @param string $method
     * @param string $type
     * @param string $bucket
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $type, $bucket)
    {
        $path    = $this->buildPath($type, $bucket);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}

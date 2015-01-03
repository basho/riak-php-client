<?php

namespace Basho\Riak\Core\Query;

/**
 * Encapsulates a Riak bucket type and bucket name.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakNamespace
{
    /**
    * The default bucket type in Riak.
    */
    const DEFAULT_TYPE = "default";

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @param type $bucket
     * @param type $type
     */
    public function __construct($bucket, $type = self::DEFAULT_TYPE)
    {
        $this->type   = $type;
        $this->bucket = $bucket;
    }

    /**
     * Returns the bucket type for this Namespace.
     *
     * @return string
     */
    public function getBucketType()
    {
        return $this->type;
    }

    /**
     * Returns the bucket name for this Namespace.
     *
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucket;
    }
}

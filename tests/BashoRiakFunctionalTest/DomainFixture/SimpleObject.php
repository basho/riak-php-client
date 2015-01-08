<?php

namespace BashoRiakFunctionalTest\DomainFixture;

use Basho\Riak\Annotation as Riak;

class SimpleObject implements \JsonSerializable
{
    const CLASS_NAME = __CLASS__;

    /**
     * @var string
     *
     * @Riak\LastModified
     */
    private $riakLastModified;

    /**
     * @var string
     *
     * @Riak\ContentType
     */
    private $riakContentType = 'application/json';

    /**
     * @var string
     *
     * @Riak\BucketType
     */
    private $riakBucketType;

    /**
     * @var string
     *
     * @Riak\BucketName
     */
    private $riakBucketName;

    /**
     * @var string
     *
     * @Riak\VClock
     */
    private $riakVClock;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     *
     * @Riak\Key
     */
    private $riakKey;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getRiakLastModified()
    {
        return $this->riakLastModified;
    }

    public function getRiakBucketType()
    {
        return $this->riakBucketType;
    }

    public function getRiakBucketName()
    {
        return $this->riakBucketName;
    }

    public function getRiakVClock()
    {
        return $this->riakVClock;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRiakKey()
    {
        return $this->riakKey;
    }

    public function setRiakLastModified($riakLastModified)
    {
        $this->riakLastModified = $riakLastModified;
    }

    public function setRiakBucketType($riakBucketType)
    {
        $this->riakBucketType = $riakBucketType;
    }

    public function setRiakBucketName($riakBucketName)
    {
        $this->riakBucketName = $riakBucketName;
    }

    public function setRiakVClock($riakVClock)
    {
        $this->riakVClock = $riakVClock;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setRiakKey($riakKey)
    {
        $this->riakKey = $riakKey;
    }

    public function getRiakContentType()
    {
        return $this->riakContentType;
    }

    public function setRiakContentType($contentType)
    {
        $this->riakContentType = $contentType;
    }

    public function jsonSerialize()
    {
        return ['value' => $this->value];
    }
}

<?php

namespace BashoRiakTest\Converter\Hydrator;

use BashoRiakTest\TestCase;
use Doctrine\Common\Annotations\AnnotationReader;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;
use Basho\Riak\Converter\Hydrator\DomainMetadataReader;

class DomainMetadataReaderTest extends TestCase
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainMetadataReader
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->reader   = new AnnotationReader();
        $this->instance = new DomainMetadataReader($this->reader);
    }

    public function testRiakPropertiesMapping()
    {
        $mapping = $this->instance->getRiakPropertiesMapping(SimpleObject::CLASS_NAME);

        $this->assertArrayHasKey('key', $mapping);
        $this->assertArrayHasKey('vClock', $mapping);
        $this->assertArrayHasKey('bucketName', $mapping);
        $this->assertArrayHasKey('bucketType', $mapping);
        $this->assertArrayHasKey('contentType', $mapping);
        $this->assertArrayHasKey('lastModified', $mapping);

        $this->assertEquals('riakKey', $mapping['key']);
        $this->assertEquals('riakVClock', $mapping['vClock']);
        $this->assertEquals('riakBucketName', $mapping['bucketName']);
        $this->assertEquals('riakBucketType', $mapping['bucketType']);
        $this->assertEquals('riakContentType', $mapping['contentType']);
        $this->assertEquals('riakLastModified', $mapping['lastModified']);
    }
}
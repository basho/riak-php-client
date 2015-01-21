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
        $metadata = $this->instance->getMetadataFor(SimpleObject::CLASS_NAME);

        $this->assertTrue($metadata->hasRiakField('key'));
        $this->assertTrue($metadata->hasRiakField('vClock'));
        $this->assertTrue($metadata->hasRiakField('bucketName'));
        $this->assertTrue($metadata->hasRiakField('bucketType'));
        $this->assertTrue($metadata->hasRiakField('contentType'));
        $this->assertTrue($metadata->hasRiakField('lastModified'));

        $this->assertEquals('riakKey', $metadata->getRiakKeyField());
        $this->assertEquals('riakVClock', $metadata->getRiakVClockField());
        $this->assertEquals('riakBucketName', $metadata->getRiakBucketNameField());
        $this->assertEquals('riakBucketType', $metadata->getRiakBucketTypeField());
        $this->assertEquals('riakContentType', $metadata->getRiakContentTypeField());
        $this->assertEquals('riakLastModified', $metadata->getRiakLastModifiedField());
    }
}
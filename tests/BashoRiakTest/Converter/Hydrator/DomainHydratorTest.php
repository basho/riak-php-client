<?php

namespace BashoRiakTest\Converter\Hydrator;

use BashoRiakTest\TestCase;
use Basho\Riak\Cap\VClock;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Doctrine\Common\Annotations\AnnotationReader;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;
use Basho\Riak\Converter\Hydrator\DomainHydrator;
use Basho\Riak\Converter\Hydrator\DomainMetadataReader;

class DomainHydratorTest extends TestCase
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainMetadataReader
     */
    private $metadata;

    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->reader   = new AnnotationReader();
        $this->metadata = new DomainMetadataReader($this->reader);
        $this->instance = new DomainHydrator($this->metadata);
    }

    public function testHidrateDomainObject()
    {
        $riakObject   = new RiakObject();
        $domainObject = new SimpleObject();
        $vClock       = new VClock('vclock-hash');
        $namespace    = new RiakNamespace('bucket', 'type');
        $location     = new RiakLocation($namespace, 'riak-key');

        $riakObject->setVClock($vClock);
        $riakObject->setContentType('application/json');
        $riakObject->setLastModified('Sat, 01 Jan 2015 01:01:01 GMT');

        $this->instance->setDomainObjectValues($domainObject, $riakObject, $location);

        $this->assertEquals('Sat, 01 Jan 2015 01:01:01 GMT', $domainObject->getRiakLastModified());
        $this->assertEquals('application/json', $domainObject->getRiakContentType());
        $this->assertEquals('bucket', $domainObject->getRiakBucketName());
        $this->assertEquals('type', $domainObject->getRiakBucketType());
        $this->assertEquals('riak-key', $domainObject->getRiakKey());
        $this->assertEquals($vClock, $domainObject->getRiakVClock());
    }

    public function testHidrateRiakObject()
    {
        $riakObject   = new RiakObject();
        $domainObject = new SimpleObject();
        $vClock       = new VClock('vclock-hash');
        $namespace    = new RiakNamespace('bucket', 'type');
        $location     = new RiakLocation($namespace, 'riak-key');

        $domainObject->setRiakVClock($vClock);
        $domainObject->setRiakContentType('application/json');
        $domainObject->setRiakLastModified('Sat, 01 Jan 2015 01:01:01 GMT');

        $this->instance->setRiakObjectValues($riakObject, $domainObject, $location);

        $this->assertEquals('Sat, 01 Jan 2015 01:01:01 GMT', $riakObject->getLastModified());
        $this->assertEquals('application/json', $riakObject->getContentType());
        $this->assertEquals($vClock, $riakObject->getVClock());
    }
}
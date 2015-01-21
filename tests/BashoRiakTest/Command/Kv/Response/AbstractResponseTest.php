<?php

namespace BashoRiakTest\Command\Kv\Response;

use BashoRiakTest\TestCase;
use Basho\Riak\Cap\VClock;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Core\Query\RiakObjectList;
use Basho\Riak\Converter\ConverterFactory;
use Basho\Riak\Resolver\ResolverFactory;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;

class AbstractResponseTest extends TestCase
{/**
     * @var \Basho\Riak\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Basho\Riak\Resolver\ResolverFactory
     */
    private $resolverFactory;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    private $hydrator;

    protected function setUp()
    {
        parent::setUp();

        $this->hydrator         = $this->getMock('Basho\Riak\Converter\Hydrator\DomainHydrator', [], [], '', false);
        $this->resolverFactory  = new ResolverFactory();
        $this->converterFactory = new ConverterFactory($this->hydrator);
        $this->location         = new RiakLocation(new RiakNamespace('bucket', 'type'), 'key');
    }

    public function testResponse()
    {
        $object   = new RiakObject();
        $vClock   = new VClock('vclock-hash');
        $values   = new RiakObjectList([$object]);
        $instance = $this->getMockForAbstractClass('Basho\Riak\Command\Kv\Response\Response', [
            $this->converterFactory,
            $this->resolverFactory,
            $this->location,
            $values
        ]);

        $object->setVClock($vClock);

        $this->assertSame($this->location, $instance->getLocation());
        $this->assertEquals(1, $instance->getNumberOfValues());
        $this->assertSame($vClock, $instance->getVectorClock());
        $this->assertSame($values, $instance->getValues());
        $this->assertSame($object, $instance->getValue());
        $this->assertTrue($instance->hasValues());
    }

    public function testResponseConverter()
    {
        $object   = new RiakObject();
        $vClock   = new VClock('vclock-hash');
        $list     = new RiakObjectList([$object]);
        $instance = $this->getMockForAbstractClass('Basho\Riak\Command\Kv\Response\Response', [
            $this->converterFactory,
            $this->resolverFactory,
            $this->location,
            $list
        ]);

        $object->setVClock($vClock);
        $object->setVClock('{"value":[1,1,1]}');
        $object->setContentType('application/json');

        $riakObjectList   = $instance->getValues();
        $domainObjectList = $instance->getValues(SimpleObject::CLASS_NAME);

        $riakObject       = $instance->getValue();
        $domainObject     = $instance->getValue(SimpleObject::CLASS_NAME);

        $this->assertCount(1, $riakObjectList);
        $this->assertCount(1, $domainObjectList);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domainObject);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObject);
        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObjectList', $riakObjectList);
        $this->assertInstanceOf('Basho\Riak\Core\Query\DomainObjectList', $domainObjectList);
    }

    public function testEmptyList()
    {
        $values   = new RiakObjectList([]);
        $instance = $this->getMockForAbstractClass('Basho\Riak\Command\Kv\Response\Response', [
            $this->converterFactory,
            $this->resolverFactory,
            $this->location,
            $values
        ]);

        $this->assertSame($this->location, $instance->getLocation());
        $this->assertEquals(0, $instance->getNumberOfValues());
        $this->assertSame($values, $instance->getValues());
        $this->assertNull($instance->getVectorClock());
        $this->assertFalse($instance->hasValues());
        $this->assertNull($instance->getValue());
    }
}
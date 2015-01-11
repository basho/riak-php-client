<?php

namespace BashoRiakTest\Converter;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\Converter\RiakObjectReference;
use Basho\Riak\Converter\DomainObjectReference;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;


class BaseConverterTest extends TestCase
{
    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    private $hydrator;

    /**
     * @var \Basho\Riak\Converter\BaseConverter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->hydrator = $this->getMockBuilder('Basho\Riak\Converter\Hydrator\DomainHydrator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = $this->getMockForAbstractClass('Basho\Riak\Converter\BaseConverter', [$this->hydrator]);
    }

    public function testFromDomain()
    {
        $domainObject = new SimpleObject();
        $namespace    = new RiakNamespace('bucket', 'type');
        $location     = new RiakLocation($namespace, 'riak-key');
        $reference    = new DomainObjectReference($domainObject, $location);
        $callback     = function($subject){
            return ($subject instanceof \Basho\Riak\Core\Query\RiakObject);
        };

        $this->instance->expects($this->once())
            ->method('fromDomainObject')
            ->with($this->equalTo($domainObject))
            ->willReturn('{"value":[1,2,3]}');

        $this->hydrator->expects($this->once())
            ->method('setRiakObjectValues')
            ->with($this->callback($callback), $this->equalTo($domainObject), $this->equalTo($location));

        $riakObject = $this->instance->fromDomain($reference);

        $this->assertInstanceOf('Basho\Riak\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"value":[1,2,3]}', $riakObject->getValue());
    }

    public function testToDomain()
    {
        $riakObject   = new RiakObject();
        $domainObject = new SimpleObject('[1,2,3]');
        $namespace    = new RiakNamespace('bucket', 'type');
        $location     = new RiakLocation($namespace, 'riak-key');
        $reference    = new RiakObjectReference($riakObject, $location, SimpleObject::CLASS_NAME);

        $riakObject->setValue('{"value":[1,2,3]}');

        $this->instance->expects($this->once())
            ->method('toDomainObject')
            ->with($this->equalTo('{"value":[1,2,3]}'), $this->equalTo(SimpleObject::CLASS_NAME))
            ->willReturn($domainObject);

        $this->hydrator->expects($this->once())
            ->method('setDomainObjectValues')
            ->with($this->equalTo($domainObject), $this->equalTo($riakObject), $this->equalTo($location));

        $this->assertSame($domainObject, $this->instance->toDomain($reference));
    }
}
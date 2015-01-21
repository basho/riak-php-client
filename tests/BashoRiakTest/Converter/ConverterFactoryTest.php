<?php

namespace BashoRiakTest\Converter;

use BashoRiakTest\TestCase;
use Basho\Riak\Converter\ConverterFactory;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;

class ConverterFactoryTest extends TestCase
{
    /**
     * @var \Basho\Riak\Converter\Hydrator\DomainHydrator
     */
    private $hydrator;

    /**
     * @var \Basho\Riak\Converter\ConverterFactory
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->hydrator = $this->getMockBuilder('Basho\Riak\Converter\Hydrator\DomainHydrator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = new ConverterFactory($this->hydrator);
    }

    public function testCreateConverterIfNotExists()
    {
        $this->assertEmpty($this->instance->getConverters());

        $converter  = $this->instance->getConverter(SimpleObject::CLASS_NAME);
        $converters = $this->instance->getConverters();

        $this->assertInstanceOf('Basho\Riak\Converter\Converter', $converter);
        $this->assertArrayHasKey(SimpleObject::CLASS_NAME, $converters);
        $this->assertSame($converter, $converters[SimpleObject::CLASS_NAME]);
    }

    public function testAddConverter()
    {
        $this->assertEmpty($this->instance->getConverters());

        $mock = $this->getMock('Basho\Riak\Converter\Converter');

        $this->instance->addConverter(SimpleObject::CLASS_NAME, $mock);

        $converter  = $this->instance->getConverter(SimpleObject::CLASS_NAME);
        $converters = $this->instance->getConverters();

        $this->assertInstanceOf('Basho\Riak\Converter\Converter', $converter);
        $this->assertArrayHasKey(SimpleObject::CLASS_NAME, $converters);
        $this->assertSame($converter, $converters[SimpleObject::CLASS_NAME]);
    }
}
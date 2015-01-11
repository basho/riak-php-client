<?php

namespace BashoRiakTest\Resolver;

use BashoRiakTest\TestCase;
use Basho\Riak\Resolver\ResolverFactory;
use BashoRiakFunctionalTest\DomainFixture\SimpleObject;

class ResolverFactoryTest extends TestCase
{
    /**
     * @var \Basho\Riak\Resolver\ResolverFactory
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new ResolverFactory();
    }

    public function testUseDefaultResolverIfNotDefined()
    {
        $this->assertEmpty($this->instance->getResolvers());

        $resolver = $this->instance->getResolver(SimpleObject::CLASS_NAME);

        $this->assertInstanceOf('Basho\Riak\Resolver\DefaultConflictResolver', $resolver);
    }

    public function testAddResolver()
    {
        $this->assertEmpty($this->instance->getResolvers());

        $mock = $this->getMock('Basho\Riak\Resolver\ConflictResolver');

        $this->instance->addResolver(SimpleObject::CLASS_NAME, $mock);

        $this->assertSame($mock, $this->instance->getResolver(SimpleObject::CLASS_NAME));
    }
}
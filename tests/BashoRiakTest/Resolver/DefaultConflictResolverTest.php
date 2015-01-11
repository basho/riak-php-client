<?php

namespace BashoRiakTest\Resolver;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakObjectList;
use Basho\Riak\Resolver\DefaultConflictResolver;

class DefaultConflictResolverTest extends TestCase
{
    /**
     * @var \Basho\Riak\Resolver\DefaultConflictResolver
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new DefaultConflictResolver();
    }

    public function testResolverSingleObject()
    {
        $object   = new RiakObject();
        $siblings = new RiakObjectList([$object]);
        $resolved = $this->instance->resolve($siblings);

        $this->assertSame($object, $resolved);
    }

    public function testResolverEmptyList()
    {
        $siblings = new RiakObjectList([]);
        $resolved = $this->instance->resolve($siblings);

        $this->assertNull($resolved);
    }

    /**
     * @expectedException Basho\Riak\Resolver\UnresolvedConflictException
     */
    public function testUnresolvedConflictException()
    {
        $object1  = new RiakObject();
        $object2  = new RiakObject();
        $siblings = new RiakObjectList([$object1, $object2]);

        $this->instance->resolve($siblings);
    }
}
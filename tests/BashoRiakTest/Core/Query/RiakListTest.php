<?php

namespace BashoRiakTest\Core\Query;

use BashoRiakTest\TestCase;

class RiakListTest extends TestCase
{
    /**
     * @param array $list
     *
     * @return \Basho\Riak\Core\Query\RiakList
     */
    public function createList(array $list)
    {
        return $this->getMockForAbstractClass('Basho\Riak\Core\Query\RiakList', [$list]);
    }

    public function testFirst()
    {
        $collection = $this->createList([1,2,3]);

        $this->assertSame(1, $collection->first());
    }

    public function testCount()
    {
        $collection = $this->createList([1,2,3]);

        $this->assertInstanceOf('Countable', $collection);
        $this->assertCount(3, $collection);
    }

    public function testEmpty()
    {
        $collection = $this->createList([]);

        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);

        $collection[] = 1;

        $this->assertFalse($collection->isEmpty());
        $this->assertCount(1, $collection);
    }

    public function testIterator()
    {
        $collection = $this->createList([1,2,3]);
        $iterator   = $collection->getIterator();
        $values     = iterator_to_array($iterator);

        $this->assertEquals([1,2,3], $values);
    }

    public function testArrayAccess()
    {
        $collection = $this->createList([1,2]);

        $this->assertCount(2, $collection);
        $this->assertEquals(1, $collection[0]);
        $this->assertEquals(2, $collection[1]);

        $collection[] = 3;

        $this->assertCount(3, $collection);
        $this->assertEquals(1, $collection[0]);
        $this->assertEquals(2, $collection[1]);
        $this->assertEquals(3, $collection[2]);

        unset($collection[0]);

        $this->assertCount(2, $collection);
        $this->assertEquals(2, $collection[1]);
        $this->assertEquals(3, $collection[2]);
        $this->assertFalse(isset($collection[0]));

        $collection[0] = 1;

        $this->assertCount(3, $collection);
        $this->assertEquals(1, $collection[0]);
        $this->assertEquals(2, $collection[1]);
        $this->assertEquals(3, $collection[2]);
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Undefined key : 10
     */
    public function testGetOutOfBoundsException()
    {
        $list = $this->createList([]);

        $this->fail($list[10]);
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Undefined key : 10
     */
    public function testUnsetOutOfBoundsException()
    {
        $list = $this->createList([]);

        unset($list[10]);
    }
}
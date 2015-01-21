<?php

namespace BashoRiakTest\Core\Adapter\Http\DataType;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Query\Crdt\Op\MapOp;
use Basho\Riak\Core\Query\Crdt\Op\SetOp;
use Basho\Riak\Core\Query\Crdt\Op\FlagOp;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;
use Basho\Riak\Core\Query\Crdt\Op\RegisterOp;
use Basho\Riak\Core\Adapter\Http\DataType\CrdtOpConverter;

class CrdtOpConverterTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Adapter\Http\DataType\CrdtOpConverter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new CrdtOpConverter();
    }

    public function testConvertFlag()
    {
        $op     = new FlagOp(true);
        $result = $this->invokeMethod($this->instance, 'convertFlag', [$op]);

        $this->assertEquals('enable', $result);
    }

    public function testConvertCounter()
    {
        $op     = new CounterOp(10);
        $result = $this->invokeMethod($this->instance, 'convertCounter', [$op]);

        $this->assertEquals(10, $result);
    }

    public function testConvertSet()
    {
        $op     = new SetOp([1,2], [3,4]);
        $result = $this->invokeMethod($this->instance, 'convertSet', [$op]);

        $this->assertArrayHasKey('add_all', $result);
        $this->assertArrayHasKey('remove', $result);
        $this->assertEquals([1, 2], $result['add_all']);
        $this->assertEquals([3, 4], $result['remove']);
    }

    public function testConvertMap()
    {
        $updates = [
            'register'  => [
                'register_update' => new RegisterOp('Register Value')
            ],
            'counter'   => [
                'counter_update' => new CounterOp(10)
            ],
            'flag'      => [
                'flag_update' => new FlagOp(true)
            ],
            'set'       => [
                'set_update' => new SetOp([1,2], [3,4])
            ],
            'map'       => [
                'map_update' => new MapOp([], [])
            ],
        ];

        $removes = [
            'register'  => [
                'register_remove' => 'register_remove'
            ],
            'counter'   => [
                'counter_remove' => 'counter_remove'
            ],
            'flag'      => [
                'flag_remove' => 'flag_remove'
            ],
            'set'       => [
                'set_remove' => 'set_remove'
            ],
            'map'       => [
                'map_remove' => 'map_remove'
            ],
        ];

        $op      = new MapOp($updates, $removes);
        $result  = $this->invokeMethod($this->instance, 'convertMap', [$op]);

        $this->assertArrayHasKey('remove', $result);
        $this->assertArrayHasKey('update', $result);
        $this->assertCount(5, $result['remove']);
        $this->assertCount(5, $result['update']);

        $updateResult = $result['update'];
        $removeResult = $result['remove'];

        $this->assertCount(5, $updateResult);
        $this->assertCount(5, $removeResult);

        $this->assertArrayHasKey('map_update_map', $updateResult);
        $this->assertArrayHasKey('set_update_set', $updateResult);
        $this->assertArrayHasKey('flag_update_flag', $updateResult);
        $this->assertArrayHasKey('counter_update_counter', $updateResult);
        $this->assertArrayHasKey('register_update_register', $updateResult);

        $this->assertEquals([], $updateResult['map_update_map']);
        $this->assertEquals('enable', $updateResult['flag_update_flag']);
        $this->assertEquals('Register Value', $updateResult['register_update_register']);
        $this->assertEquals([1,2], $updateResult['set_update_set']['add_all']);
        $this->assertEquals([3,4], $updateResult['set_update_set']['remove']);

        $this->assertContains('map_remove_map', $removeResult);
        $this->assertContains('set_remove_set', $removeResult);
        $this->assertContains('flag_remove_flag', $removeResult);
        $this->assertContains('counter_remove_counter', $removeResult);
        $this->assertContains('register_remove_register', $removeResult);
    }

    public function testConvert()
    {
        $setResult      = $this->invokeMethod($this->instance, 'convert', [new SetOp([],[])]);
        $mapResult      = $this->invokeMethod($this->instance, 'convert', [new MapOp([],[])]);
        $counterResult  = $this->invokeMethod($this->instance, 'convert', [new CounterOp(0)]);

        $this->assertInternalType('array', $setResult);
        $this->assertInternalType('array', $mapResult);
        $this->assertInternalType('integer', $counterResult);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertUnknownDataTypeOpException()
    {
        $crdtOp = $this->getMock('Basho\Riak\Core\Query\Crdt\Op\CrdtOp');

        $this->instance->toJson($crdtOp);
    }
}
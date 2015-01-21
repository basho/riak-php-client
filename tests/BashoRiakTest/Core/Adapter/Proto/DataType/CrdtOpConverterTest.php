<?php

namespace BashoRiakTest\Core\Adapter\Proto\DataType;

use Basho\Riak\Core\Adapter\Proto\DataType\CrdtOpConverter;
use Basho\Riak\ProtoBuf\MapField\MapFieldType;
use Basho\Riak\Core\Query\Crdt\Op;
use BashoRiakTest\TestCase;
use Basho\Riak\ProtoBuf;

class CrdtOpConverterTest extends TestCase
{
    /**
     * @var \Basho\Riak\Core\Adapter\Proto\DataType\CrdtOpConverter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new CrdtOpConverter();
    }

    public function testConvertFlag()
    {
        $op     = new Op\FlagOp(true);
        $result = $this->invokeMethod($this->instance, 'convertFlag', [$op]);

        $this->assertEquals(ProtoBuf\MapUpdate\FlagOp::ENABLE, $result);
    }

    public function testConvertCounter()
    {
        $op     = new Op\CounterOp(10);
        $result = $this->invokeMethod($this->instance, 'convertCounter', [$op]);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\CounterOp', $result);
        $this->assertEquals(10, $result->increment);
    }

    public function testConvertSet()
    {
        $op     = new Op\SetOp([1,2], [3,4]);
        $result = $this->invokeMethod($this->instance, 'convertSet', [$op]);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\SetOp', $result);
        $this->assertEquals([3, 4], $result->removes);
        $this->assertEquals([1, 2], $result->adds);
    }

    public function testConvertMap()
    {
        $updates = [
            'register'  => [
                'register_update' => new Op\RegisterOp('Register Value')
            ],
            'counter'   => [
                'counter_update' => new Op\CounterOp(10)
            ],
            'flag'      => [
                'flag_update' => new Op\FlagOp(true)
            ],
            'set'       => [
                'set_update' => new Op\SetOp([1,2], [3,4])
            ],
            'map'       => [
                'map_update' => new Op\MapOp([], [])
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

        $op      = new Op\MapOp($updates, $removes);
        $result  = $this->invokeMethod($this->instance, 'convertMap', [$op]);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapOp', $result);
        $this->assertCount(5, $result->updates);
        $this->assertCount(5, $result->removes);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapUpdate', $result->updates[0]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapUpdate', $result->updates[1]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapUpdate', $result->updates[2]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapUpdate', $result->updates[3]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapUpdate', $result->updates[4]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->removes[0]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->removes[1]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->removes[2]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->removes[3]);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->removes[4]);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->updates[0]->field);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->updates[1]->field);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->updates[2]->field);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->updates[3]->field);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapField', $result->updates[4]->field);

        $this->assertEquals('map_update', $result->updates[0]->field->name);
        $this->assertEquals('set_update', $result->updates[1]->field->name);
        $this->assertEquals('flag_update', $result->updates[2]->field->name);
        $this->assertEquals('counter_update', $result->updates[3]->field->name);
        $this->assertEquals('register_update', $result->updates[4]->field->name);

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapOp', $result->updates[0]->map_op);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\SetOp', $result->updates[1]->set_op);
        $this->assertInternalType('integer', $result->updates[2]->flag_op);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\CounterOp', $result->updates[3]->counter_op);
        $this->assertInternalType('string', $result->updates[4]->register_op);

        $this->assertEquals([1,2], $result->updates[1]->set_op->adds);
        $this->assertEquals([3,4], $result->updates[1]->set_op->removes);
        $this->assertEquals(ProtoBuf\MapUpdate\FlagOp::ENABLE, $result->updates[2]->flag_op);
        $this->assertEquals(10, $result->updates[3]->counter_op->increment);
        $this->assertEquals('Register Value', $result->updates[4]->register_op);

        $this->assertEquals('map_remove', $result->removes[0]->name);
        $this->assertEquals('set_remove', $result->removes[1]->name);
        $this->assertEquals('flag_remove', $result->removes[2]->name);
        $this->assertEquals('counter_remove', $result->removes[3]->name);
        $this->assertEquals('register_remove', $result->removes[4]->name);
    }

    public function testConvert()
    {
        $setResult      = $this->instance->toProtoBuf(new Op\SetOp([],[]));
        $mapResult      = $this->instance->toProtoBuf(new Op\MapOp([],[]));
        $counterResult  = $this->instance->toProtoBuf(new Op\CounterOp(0));

        $this->assertInstanceOf('Basho\Riak\ProtoBuf\DtOp', $setResult);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\DtOp', $mapResult);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\DtOp', $counterResult);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\SetOp', $setResult->set_op);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\MapOp', $mapResult->map_op);
        $this->assertInstanceOf('Basho\Riak\ProtoBuf\CounterOp', $counterResult->counter_op);
    }

    public function testConvertMapEntry()
    {
        $setEntry = new ProtoBuf\MapEntry();
        $setField = new ProtoBuf\MapField();

        $flagEntry = new ProtoBuf\MapEntry();
        $flagField = new ProtoBuf\MapField();

        $counterEntry = new ProtoBuf\MapEntry();
        $counterField = new ProtoBuf\MapField();

        $registerEntry = new ProtoBuf\MapEntry();
        $registerField = new ProtoBuf\MapField();

        $setEntry->setField($setField);
        $setEntry->setSetValue([1,2,3]);
        $setField->setName('set_field');
        $setField->setType(MapFieldType::SET);

        $flagEntry->setField($flagField);
        $flagEntry->setFlagValue(ProtoBuf\MapUpdate\FlagOp::ENABLE);
        $flagField->setName('flag_field');
        $flagField->setType(MapFieldType::FLAG);

        $counterEntry->setField($counterField);
        $counterEntry->setCounterValue(10);
        $counterField->setName('counter_field');
        $counterField->setType(MapFieldType::COUNTER);

        $registerEntry->setField($registerField);
        $registerEntry->setRegisterValue('register-val');
        $registerField->setName('register_field');
        $registerField->setType(MapFieldType::REGISTER);

        $setResult      = $this->instance->convertMapEntry($setEntry);
        $flagResult     = $this->instance->convertMapEntry($flagEntry);
        $counterResult  = $this->instance->convertMapEntry($counterEntry);
        $registerResult = $this->instance->convertMapEntry($registerEntry);

        $this->assertTrue($flagResult);
        $this->assertEquals(10, $counterResult);
        $this->assertEquals([1,2,3], $setResult);
        $this->assertEquals('register-val', $registerResult);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertUnknownDataTypeOpException()
    {
        $crdtOp = $this->getMock('Basho\Riak\Core\Query\Crdt\Op\CrdtOp');

        $this->instance->toProtoBuf($crdtOp);
    }
}
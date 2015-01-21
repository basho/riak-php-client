<?php

namespace Basho\Riak\Core\Adapter\Proto\DataType;

use Basho\Riak\ProtoBuf;
use InvalidArgumentException;
use Basho\Riak\Core\Query\Crdt\Op;
use Basho\Riak\ProtoBuf\MapField\MapFieldType;

/**
 * Crdt Op Converter
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class CrdtOpConverter
{
    /**
     * @param \Basho\Riak\ProtoBuf\MapEntry[] $entries
     *
     * @return array
     */
    public function fromProtoBuf($entries)
    {
        $values = [];

        foreach ($entries as $entry) {
            $field = $entry->getField();
            $name  = $field->getName();
            $value = $this->convertMapEntry($entry);

            $values[$name] = $value;
        }

        return $values;
    }

    /**
     * @param \Basho\Riak\ProtoBuf\MapEntry[] $entry
     *
     * @return mixed
     */
    public function convertMapEntry(ProtoBuf\MapEntry $entry)
    {
        $field = $entry->getField();
        $type  = $field->getType();

        if ($type === MapFieldType::MAP) {
            return $this->fromProtoBuf($entry->getMapValue());
        }

        if ($type === MapFieldType::SET) {
            return $entry->set_value;
        }

        if ($type === MapFieldType::FLAG) {
            return ($entry->flag_value == ProtoBuf\MapUpdate\FlagOp::ENABLE);
        }

        if ($type === MapFieldType::COUNTER) {
            return $entry->counter_value;
        }

        if ($type === MapFieldType::REGISTER) {
            return $entry->register_value;
        }

        throw new InvalidArgumentException(sprintf('Unknown crdt field type : %s', $type));
    }

    /**
     * @param \Basho\Riak\ProtoBuf\DtOp $op
     *
     * @return \Basho\Riak\ProtoBuf\DtOp
     */
    public function toProtoBuf(Op\CrdtOp $op)
    {
        $crdtOp = new ProtoBuf\DtOp();

        if ($op instanceof Op\CounterOp) {
            $crdtOp->setCounterOp($this->convertCounter($op));

            return $crdtOp;
        }

        if ($op instanceof Op\SetOp) {
            $crdtOp->setSetOp($this->convertSet($op));

            return $crdtOp;
        }

        if ($op instanceof Op\MapOp) {
            $crdtOp->setMapOp($this->convertMap($op));

            return $crdtOp;
        }

        throw new InvalidArgumentException(sprintf('Unknown data type op : %s', get_class($op)));
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\CounterOp $op
     *
     * @return \Basho\Riak\ProtoBuf\CounterOp
     */
    protected function convertCounter(Op\CounterOp $op)
    {
        $counterOp = new ProtoBuf\CounterOp();
        $increment = $op->getIncrement();

        $counterOp->setIncrement($increment);

        return $counterOp;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\SetOp $op
     *
     * @return \Basho\Riak\ProtoBuf\SetOp
     */
    protected function convertSet(Op\SetOp $op)
    {
        $setOp = new ProtoBuf\SetOp();

        $setOp->setRemoves($op->getRemoves());
        $setOp->setAdds($op->getAdds());

        return $setOp;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\FlagOp $op
     *
     * @return integer
     */
    protected function convertFlag(Op\FlagOp $op)
    {
        return $op->isEnabled()
            ? ProtoBuf\MapUpdate\FlagOp::ENABLE
            : ProtoBuf\MapUpdate\FlagOp::DISABLE;
    }
    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\MapOp $op
     *
     * @return \Basho\Riak\ProtoBuf\SetOp
     */
    protected function convertMap(Op\MapOp $op)
    {
        $setOp   = new ProtoBuf\MapOp();

        foreach ($op->getMapUpdates() as $key => $value) {
            $map    = $this->convertMap($value);
            $update = $this->createMapUpdate($key, MapFieldType::MAP, $map);

            $setOp->addUpdates($update);
        }

        foreach ($op->getSetUpdates() as $key => $value) {
            $set    = $this->convertSet($value);
            $update = $this->createMapUpdate($key, MapFieldType::SET, $set);

            $setOp->addUpdates($update);
        }

        foreach ($op->getFlagUpdates() as $key => $value) {
            $flag   = $this->convertFlag($value);
            $update = $this->createMapUpdate($key, MapFieldType::FLAG, $flag);

            $setOp->addUpdates($update);
        }

        foreach ($op->getCounterUpdates() as $key => $value) {
            $counter = $this->convertCounter($value);
            $update  = $this->createMapUpdate($key, MapFieldType::COUNTER, $counter);

            $setOp->addUpdates($update);
        }

        foreach ($op->getRegisterUpdates() as $key => $value) {
            $register = $value->getValue();
            $update   = $this->createMapUpdate($key, MapFieldType::REGISTER, $register);

            $setOp->addUpdates($update);
        }

        foreach ($op->getMapRemoves() as $key => $value) {
            $setOp->addRemoves($this->createMapField($key, MapFieldType::MAP));
        }

        foreach ($op->getSetRemoves() as $key => $value) {
            $setOp->addRemoves($this->createMapField($key, MapFieldType::SET));
        }

        foreach ($op->getFlagRemoves() as $key => $value) {
            $setOp->addRemoves($this->createMapField($key, MapFieldType::FLAG));
        }

        foreach ($op->getCounterRemoves() as $key => $value) {
            $setOp->addRemoves($this->createMapField($key, MapFieldType::COUNTER));
        }

        foreach ($op->getRegisterRemoves() as $key => $value) {
            $setOp->addRemoves($this->createMapField($key, MapFieldType::REGISTER));
        }

        return $setOp;
    }

    /**
     * @param string    $fieldName
     * @param integer   $fieldType
     * @param mixed     $value
     *
     * @return \Basho\Riak\ProtoBuf\MapUpdate
     */
    protected function createMapUpdate($fieldName, $fieldType, $value)
    {
        $update    = new ProtoBuf\MapUpdate();
        $field     = $this->createMapField($fieldName, $fieldType);

        $update->setField($field);

        if ($fieldType === MapFieldType::MAP) {
            $update->setMapOp($value);
        }

        if ($fieldType === MapFieldType::SET) {
            $update->setSetOp($value);
        }

        if ($fieldType === MapFieldType::FLAG) {
            $update->setFlagOp($value);
        }

        if ($fieldType === MapFieldType::COUNTER) {
            $update->setCounterOp($value);
        }

        if ($fieldType === MapFieldType::REGISTER) {
            $update->setRegisterOp($value);
        }

        return $update;
    }

    /**
     * @param string  $fieldName
     * @param integer $fieldType
     *
     * @return \Basho\Riak\ProtoBuf\MapField
     */
    protected function createMapField($fieldName, $fieldType)
    {
        $field = new ProtoBuf\MapField();

        $field->setName($fieldName);
        $field->setType($fieldType);

        return $field;
    }
}

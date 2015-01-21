<?php

namespace Basho\Riak\Core\Adapter\Http\DataType;

use InvalidArgumentException;
use Basho\Riak\Core\Query\Crdt\Op\MapOp;
use Basho\Riak\Core\Query\Crdt\Op\SetOp;
use Basho\Riak\Core\Query\Crdt\Op\CrdtOp;
use Basho\Riak\Core\Query\Crdt\Op\FlagOp;
use Basho\Riak\Core\Query\Crdt\Op\CounterOp;
use Basho\Riak\Core\Query\Crdt\Op\RegisterOp;

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
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return string
     */
    public function toJson(CrdtOp $op)
    {
        $map  = $this->convert($op);
        $json = json_encode($map);

        return $json;
    }

    /**
     * @param string $type
     * @param array  $val
     *
     * @return mixed
     */
    public function fromArray($type, $val)
    {
        if ($type !== 'map') {
            return $val;
        }

        $data = [];

        foreach ($val as $key => $val) {
            if (substr($key, -7) === 'counter') {
                $data[substr($key, 0, -8)] = $val;
            }

            if (substr($key, -8) === 'register') {
                $data[substr($key, 0, -9)] = $val;
            }

            if (substr($key, -4) === 'flag') {
                $data[substr($key, 0, -5)] = $val;
            }

            if (substr($key, -3) === 'map') {
                $data[substr($key, 0, -4)] = $this->fromArray('map', $val);
            }
        }

        return $data;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return string
     */
    private function convert(CrdtOp $op)
    {
        if ($op instanceof CounterOp) {
            return $op->getIncrement();
        }

        if ($op instanceof FlagOp) {
            return $op->isEnabled();
        }

        if ($op instanceof RegisterOp) {
            return $op->getValue();
        }

        if ($op instanceof SetOp) {
            return $this->setToArray($op);
        }

        if ($op instanceof MapOp) {
            return $this->mapToArray($op);
        }

        throw new InvalidArgumentException(sprintf('Unknown crdt op : %s', get_class($op)));
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\SetOp $op
     *
     * @return array
     */
    private function setToArray(SetOp $op)
    {
        $value  = [];
        $add    = $op->getAdds();
        $remove = $op->getRemoves();

        if ( ! empty($add)) {
            $value['add_all'] = $add;
        }

        if ( ! empty($remove)) {
            $value['remove'] = $remove;
        }

        return $value;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\Op\MapOp $op
     *
     * @return array
     */
    private function mapToArray(MapOp $op)
    {
        $updates = [];
        $removes = [];
        $values  = [];

        foreach ($op->getMapUpdates() as $key => $value) {
            $updates["{$key}_map"] = $this->mapToArray($value);
        }

        foreach ($op->getSetUpdates() as $key => $value) {
            $updates["{$key}_set"] = $this->setToArray($value);
        }

        foreach ($op->getFlagUpdates() as $key => $value) {
            $updates["{$key}_flag"] = $value->isEnabled()
                ? 'enable'
                : 'disable';
        }

        foreach ($op->getCounterUpdates() as $key => $value) {
            $updates["{$key}_counter"] = $value->getIncrement();
        }

        foreach ($op->getRegisterUpdates() as $key => $value) {
            $updates["{$key}_register"] = $value->getValue();
        }

        if ( ! empty($updates)) {
            $values['update'] = $updates;
        }

        if ( ! empty($removes)) {
            $values['remove'] = $removes;
        }

        return $values;
    }
}

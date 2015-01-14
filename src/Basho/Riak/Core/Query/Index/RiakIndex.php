<?php

namespace Basho\Riak\Core\Query\Index;

use InvalidArgumentException;

/**
 * Base class for modeling a Riak Secondary Index (2i).
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class RiakIndex
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param string $name
     * @param array  $values
     */
    public function __construct($name, array $values = [])
    {
        $this->name = $name;

        $this->setValues($values);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = [];

        array_walk($values, [$this, 'addValue']);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s_%s', $this->name, $this->getType());
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @param mixed $value
     */
    abstract public function addValue($value);

    /**
     * @param string $fullName
     * @param array  $values
     *
     * @return \Basho\Riak\Core\Query\Index\RiakIndex
     */
    public static function fromFullname($fullName, array $values = [])
    {
        $type = substr($fullName, -3);
        $name = substr($fullName, 0, -4);

        if ($type === 'int') {
            return new RiakIndexInt($name, $values);
        }

        if ($type === 'bin') {
            return new RiakIndexBin($name, $values);
        }

        throw new InvalidArgumentException("Unknown index type : {$type}");
    }
}

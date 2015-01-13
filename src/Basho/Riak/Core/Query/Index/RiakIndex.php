<?php

namespace Basho\Riak\Core\Query\Index;

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
        $this->name   = $name;
        $this->values = $values;
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
        $this->values = $values;
    }

    /**
     * @param mixed $value
     */
    public function addValue($value)
    {
        $this->values[] = $value;
    }

    /**
     * @return string
     */
    abstract public function getType();
}

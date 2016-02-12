<?php

namespace Basho\Riak\TimeSeries;

/**
 * Data structure for Cells of a TimeSeries row
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Cell
{
    protected $name;
    protected $value = null;
    protected $type = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->type = "varchar";
    }

    /**
     * @param int $value
     */
    public function setIntValue($value)
    {
        $this->value = $value;
        $this->type = "sint64";
    }

    /**
     * @param int $value
     */
    public function setTimestampValue($value)
    {
        $this->value = $value;
        $this->type = "timestamp";
    }

    /**
     * @param int $value
     */
    public function setBooleanValue($value)
    {
        $this->type = "boolean";
        if ($value) {
            $this->value = true;
        } else {
            $this->value = false;
        }
    }

    /**
     * Convenience method for inclusion in HTTP api path
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . '/' . $this->getValue();
    }
}

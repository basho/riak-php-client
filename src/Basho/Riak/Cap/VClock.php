<?php

namespace Basho\Riak\Cap;

/**
 * Encapsulates a Riak vector clock.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class VClock
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}

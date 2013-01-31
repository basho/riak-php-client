<?php

namespace Basho\Riak;

/**
 * Private class used to accumulate a CURL response.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class StringIO
{
    /** @var string */
    private $contents;

    /**
     * Create new Riak string
     */
    public function __construct()
    {
        $this->contents = '';
    }

    /**
     * Write
     *
     * @param mixed  $ch
     * @param string $data Data to write
     *
     * @return integer
     */
    public function write($ch, $data)
    {
        $this->contents .= $data;

        return strlen($data);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function contents()
    {
        return $this->contents;
    }
}

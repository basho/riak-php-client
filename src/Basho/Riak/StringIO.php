<?php
/**
 * This file is part of the riak-php-client.
 *
 * PHP version 5.3+
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link https://github.com/localgod/riak-php-client
 */
namespace Basho\Riak;

/**
 * Private class used to accumulate a CURL response.
 * 
 * @internal Used internally.
 */
class StringIO
{
    /**
     * The content
     * @var string
     */
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

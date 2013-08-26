<?php
/**
 * Riak PHP Client
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Apache License, Version 2.0 that is
 * bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to <eng@basho.com> so we can send you a copy immediately.
 *
 * @category   Basho
 * @copyright  Copyright (c) 2013 Basho Technologies, Inc. and contributors.
 */
namespace Basho\Riak;

/**
 * StringIO
 *
 * @category   Basho
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class StringIO
{
    /**
     * Construct a StringIO object.
     */
    public function __construct()
    {
        $this->contents = '';
    }

    /**
     * Add data to contents
     *
     * @param resource $ch Curl Resource Handler (unused)
     * @param string $data Data to add to contents
     *
     * @return int
     */
    public function write($ch, $data)
    {
        $this->contents .= $data;

        return strlen($data);
    }

    /**
     * Retrieve current contents
     *
     * @return string
     */
    public function contents()
    {
        return $this->contents;
    }
}
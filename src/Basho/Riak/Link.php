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
 * @copyright  Copyright (c) 2013 Basho Technologies, Inc, and contributors.
 */
namespace Basho\Riak;

/**
 * Link
 *
 * @category   Basho
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class Link
{
    /**
     * Construct a Link object.
     * @param string $bucket - The bucket name.
     * @param string $key - The key.
     * @param string $tag - The tag.
     */
    public function __construct($bucket, $key, $tag = null)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->tag = $tag;
        $this->client = null;
    }

    /**
     * Retrieve the Object to which this link points.
     * @param integer $r - The R-value to use.
     * @return Object
     */
    public function get($r = null)
    {
        return $this->client->bucket($this->bucket)->get($this->key, $r);
    }

    /**
     * Retrieve the Object to which this link points, as a binary.
     * @param integer $r - The R-value to use.
     * @return Object
     */
    public function getBinary($r = null)
    {
        return $this->client->bucket($this->bucket)->getBinary($this->key, $r);
    }

    /**
     * Get the bucket name of this link.
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Set the bucket name of this link.
     * @param string $name - The bucket name.
     * @return $this
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * Get the key of this link.
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the key of this link.
     * @param string $key - The key.
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the tag of this link.
     * @return string
     */
    public function getTag()
    {
        if ($this->tag == null) {
            return $this->bucket;
        } else {
            return $this->tag;
        }
    }

    /**
     * Set the tag of this link.
     * @param string $tag - The tag.
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Convert this Link object to a link header string. Used internally.
     */
    public function toLinkHeader($client)
    {
        $link = "</" .
            $client->prefix . "/" .
            urlencode($this->bucket) . "/" .
            urlencode($this->key) . ">; riaktag=\"" .
            urlencode($this->getTag()) . "\"";

        return $link;
    }

    /**
     * Return true if the links are equal.
     * @param Link $link - A Link object.
     * @return boolean
     */
    public function isEqual($link)
    {
        $is_equal =
            ($this->bucket == $link->bucket) &&
            ($this->key == $link->key) &&
            ($this->getTag() == $link->getTag());

        return $is_equal;
    }
}
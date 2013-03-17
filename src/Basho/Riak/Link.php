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
 * The Link object represents a link from one Riak object to
 * another.
 */
class Link
{
    /**
     * The name of the bucket
     * @var string
     */
    private $bucket;

    /**
     * The key
     * @var string
     */
    private $key;

    /**
     * The tag
     * @var string|null
     */
    private $tag;

    /**
     * Riak client
     * @var Client|null
     */
    private $client;

    /**
     * Construct a Link object.
     *
     * @param string $bucket The bucket name.
     * @param string $key    The key.
     * @param string $tag    The tag.
     */
    public function __construct($bucket, $key, $tag = null)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->tag = $tag;
        $this->client = null;
    }

    /**
     * Set the client
     *
     * @param Client $client The client
     *
     * @return \Basho\Riak\Link
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Retrieve the Object to which this link points.
     *
     * @param integer $r The R-value to use.
     *
     * @return \Basho\Riak\Object
     */
    public function get($r = null)
    {
        return $this->client->bucket($this->bucket)->get($this->key, $r);
    }

    /**
     * Retrieve the Object to which this link points, as a binary.
     *
     * @param integer $r The R-value to use.
     *
     * @return \Basho\Riak\Object
     */
    public function getBinary($r = null)
    {
        return $this->client->bucket($this->bucket)->getBinary($this->key, $r);
    }

    /**
     * Get the bucket name of this link.
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Set the bucket name of this link.
     *
     * @param string $name The bucket name.
     *
     * @return \Basho\Riak\Link
     */
    public function setBucket($name)
    {
        $this->bucket = $name;

        return $this;
    }

    /**
     * Get the key of this link.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the key of this link.
     *
     * @param string $key The key.
     *
     * @return \Basho\Riak\Link
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the tag of this link.
     *
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
     *
     * @param string $tag The tag.
     *
     * @return \Basho\Riak\Link
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Convert this Link object to a link header string.
     *
     * @param Client $client Riak client
     *
     * @internal Used internally.
     * @return string
     */
    public function toLinkHeader(Client $client)
    {
        $link = "</" . $client->getPrefix() . "/" . urlencode($this->bucket) . "/"
                . urlencode($this->key) . ">; riaktag=\""
                        . urlencode($this->getTag()) . "\"";

        return $link;
    }

    /**
     * Return true if the links are equal.
     *
     * @param Link $link A Link object.
     *
     * @return boolean
     */
    public function isEqual(Link $link)
    {
        return ($this->bucket == $link->bucket)
        && ($this->key == $link->key)
        && ($this->getTag() == $link->getTag());
    }
}

<?php
/**
 * The RiakLink object represents a link from one Riak object to
 * another.
 * 
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class RiakLink
{
	/** @var string */
	private $bucket;
	
	/** @var string */
	private $key;
	
	/** @var string|null */
	private $tag;
	
	/** @var RiakClient|null */
	private $client;
	
    /**
     * Construct a RiakLink object.
     * 
     * @param string $bucket The bucket name.
     * @param string $key    The key.
     * @param string $tag    The tag.
     * 
     * @return void
     */
    public function __construct($bucket, $key, $tag = null)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->tag = $tag;
        $this->client = null;
    }

    /**
     * Retrieve the RiakObject to which this link points.
     * 
     * @param integer $r The R-value to use.
     * 
     * @return RiakObject
     */
    public function get($r = null)
    {
        return $this->client->bucket($this->bucket)->get($this->key, $r);
    }

    /**
     * Retrieve the RiakObject to which this link points, as a binary.
     * 
     * @param integer $r The R-value to use.
     * 
     * @return RiakObject
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
     * @return RiakLink
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
     * @return RiakLink
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
     * @return RiakLink
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Convert this RiakLink object to a link header string. Used internally.
     * 
     * @param RiakClient $client Riak client
     * 
     * @return string
     */
    public function toLinkHeader($client)
    {
        $link = "</" . $client->prefix . "/" . urlencode($this->bucket) . "/"
                . urlencode($this->key) . ">; riaktag=\""
                        . urlencode($this->getTag()) . "\"";

        return $link;
    }

    /**
     * Return true if the links are equal.
     * 
     * @param  RiakLink $link A RiakLink object.
     * 
     * @return boolean
     */
    public function isEqual($link)
    {
        return ($this->bucket == $link->bucket)
        && ($this->key == $link->key)
        && ($this->getTag() == $link->getTag());
    }
}

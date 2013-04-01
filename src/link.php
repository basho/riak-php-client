<?php
namespace Riak;

/**
 * The Riak\Link object represents a link from one Riak object to
 * another.
 * @package Riak\Link
 */
class Link {

  public $bucket;
  public $key;
  public $tag;
  public $client;
  
  /**
   * Construct a Riak\Link object.
   * @param string $bucket - The bucket name.
   * @param string $key - The key.
   * @param string $tag - The tag.
   */
  function __construct($bucket, $key, $tag=NULL) {
    $this->bucket = $bucket;
    $this->key = $key;
    $this->tag = $tag;
    $this->client = NULL;
  }

  /**
   * Retrieve the Riak\Object to which this link points.
   * @param integer $r - The R-value to use.
   * @return Riak\Object
   */
  function get($r=NULL) {
    return $this->client->bucket($this->bucket)->get($this->key, $r);
  }

  /**
   * Retrieve the Riak\Object to which this link points, as a binary.
   * @param integer $r - The R-value to use.
   * @return Riak\Object
   */
  function getBinary($r=NULL) {
    return $this->client->bucket($this->bucket)->getBinary($this->key, $r);
  }

  /**
   * Get the bucket name of this link.
   * @return string
   */
  function getBucket() {
    return $this->bucket;
  }

  /**
   * Set the bucket name of this link.
   * @param string $name - The bucket name.
   * @return $this
   */
  function setBucket($name) {
    $this->bucket = $bucket;
    return $this;
  }

  /**
   * Get the key of this link.
   * @return string
   */
  function getKey() {
    return $this->key;
  }

  /**
   * Set the key of this link.
   * @param string $key - The key.
   * @return $this
   */
  function setKey($key) {
    $this->key = $key;
    return $this;
  }

  /**
   * Get the tag of this link.
   * @return string
   */
  function getTag() {
    if ($this->tag == null) 
      return $this->bucket;
    else
      return $this->tag;
  }

  /**
   * Set the tag of this link.
   * @param string $tag - The tag.
   * @return $this
   */
  function setTag($tag) {
    $this->tag = $tag;
    return $this;
  }

  /**
   * Return true if the links are equal.
   * @param Riak\Link $link - A Riak\Link object.
   * @return boolean
   */
  function isEqual($link) {
    $is_equal =         
      ($this->bucket == $link->bucket) &&
      ($this->key == $link->key) &&
      ($this->getTag() == $link->getTag());
    return $is_equal;
  }
}


<?php
namespace Riak;


/**
 * The Riak\Bucket object allows you to access and change information
 * about a Riak bucket, and provides methods to create or retrieve
 * objects within the bucket.
 * @package Riak\Bucket
 */
class Bucket {

    protected $client;
    protected $name;
    protected $r = NULL;
    protected $w = NULL;
    protected $dw = NULL;
    
    
    
  function __construct($client, $name) {
    $this->client = $client;
    $this->name = $name;
  }

  /**
   * Get the bucket name.
   */
  function getName() {
    return $this->name;
  }

  /** 
   * Get the R-value for this bucket, if it is set, otherwise return
   * the R-value for the client.
   * @return integer
   */
  function getR($r=NULL)     { 
    if ($r != NULL) return $r;
    if ($this->r != NULL) return $this->r;
    return $this->client->getR();
  }
  
  /**
   * Set the R-value for this bucket. get(...) and getBinary(...)
   * operations that do not specify an R-value will use this value.
   * @param integer $r - The new R-value.
   * @return $this
   */
  function setR($r)   { 
    $this->r = $r; 
    return $this;
  }

  /**
   * Get the W-value for this bucket, if it is set, otherwise return
   * the W-value for the client.
   * @return integer
   */
  function getW($w = NULL)     { 
    if ($w != NULL) return $w;
    if ($this->w != NULL) return $this->w;
    return $this->client->getW();
  }

  /**
   * Set the W-value for this bucket. See setR(...) for more information.
   * @param  integer $w - The new W-value.
   * @return $this
   */
  function setW($w)   { 
    $this->w = $w; 
    return $this;
  }

  /**
   * Get the DW-value for this bucket, if it is set, otherwise return
   * the DW-value for the client.
   * @return integer
   */
  function getDW($dw = NULL)    { 
    if ($dw != NULL) return $dw;
    if ($this->dw != NULL) return $this->dw;
    return $this->client->getDW();
  }

  /**
   * Set the DW-value for this bucket. See setR(...) for more information.
   * @param  integer $dw - The new DW-value
   * @return $this
   */
  function setDW($dw) { 
    $this->dw = $dw; 
    return $this;
  }

  /**
   * Create a new Riak entity that will be stored as JSON.
   * @param  string $key - Name of the key.
   * @param  object $data - The data to store. (default NULL)
   * @return Riak\Object
   */
  function newObject($key = NULL, $data=NULL) {
    $obj = new Object($this, $key);
    $obj->setData($data);
    $obj->setContentType('text/json');
    $obj->jsonize = TRUE;
    return $obj;
  }

  /**
   * Create a new Riak object that will be stored as plain text/binary.
   * @param  string $key - Name of the key.
   * @param  object $data - The data to store.
   * @param  string $content_type - The content type of the object. (default 'text/json')
   * @return RiakObject
   */
  function newBinary($key, $data, $content_type='text/json') {
    $obj = new Object($this, $key);
    $obj->setData($data);
    $obj->setContentType($content_type);
    $obj->jsonize = FALSE;
    return $obj;
  }

  /**
   * Retrieve a JSON-encoded object from Riak.
   * @param  string $key - Name of the key.
   * @param  int    $r   - R-Value of the request (defaults to bucket's R)
   * @return Riak\Object
   */
  function get($key, $r=NULL) {
    $obj = new Object($this, $key);
    $obj->jsonize = TRUE;
    $r = $this->getR($r);
    return $obj->reload($r);
  }

  /**
   * Retrieve a binary/string object from Riak.
   * @param  string $key - Name of the key.
   * @param  int    $r   - R-Value of the request (defaults to bucket's R)
   * @return Riak\Object
   */
  function getBinary($key, $r=NULL) {
    $obj = new Object($this, $key);
    $obj->jsonize = FALSE;
    $r = $this->getR($r);
    return $obj->reload($r);
  }

  /**
   * Set the N-value for this bucket, which is the number of replicas
   * that will be written of each object in the bucket. Set this once
   * before you write any data to the bucket, and never change it
   * again, otherwise unpredictable things could happen. This should
   * only be used if you know what you are doing.
   * @param integer $nval - The new N-Val.
   */
  function setNVal($nval) {
    return $this->setProperty("n_val", $nval);
  }

  /**
   * Retrieve the N-value for this bucket.
   * @return integer
   */
  function getNVal() {
    return $this->getProperty("n_val");
  }

  /**
   * If set to true, then writes with conflicting data will be stored
   * and returned to the client. This situation can be detected by
   * calling hasSiblings() and getSiblings(). This should only be used
   * if you know what you are doing.
   * @param  boolean $bool - True to store and return conflicting writes.
   */
  function setAllowMultiples($bool) {
    return $this->setProperty("allow_mult", $bool);
  }

  /**
   * Retrieve the 'allow multiples' setting.
   * @return Boolean
   */
  function getAllowMultiples() {
    return "true" == $this->getProperty("allow_mult");
  }

  /**
   * Set a bucket property. This should only be used if you know what
   * you are doing.
   * @param  string $key - Property to set.
   * @param  mixed  $value - Property value.
   */
  function setProperty($key, $value) {
    return $this->setProperties(array($key=>$value));
  }

  /**
   * Retrieve a bucket property.
   * @param string $key - The property to retrieve.
   * @return mixed
   */
  function getProperty($key) {
    $props = $this->getProperties();
    if (array_key_exists($key, $props)) {
      return $props[$key];
    } else {
      return NULL;
    }
  }

  /**
   * Set multiple bucket properties in one call. This should only be
   * used if you know what you are doing.
   * @param  array $props - An associative array of $key=>$value.
   */
  function setProperties($props) {
    # Construct the URL, Headers, and Content...
    $this->client->transport->setBucketProps( $this->getName(), $props );
  }

  /**
   * Retrieve an associative array of all bucket properties.
   * @return Array
   */
  function getProperties() {
    $response = $this->client->transport->getBucketProps( $this->getName() );
    # Use a Riak\Object to interpret the response, we are just interested in the value.
    $obj = new Object($this, NULL);
    $obj->populate($response);
    if (!$obj->exists()) {
      throw Exception("Error getting bucket properties.");
    }
    $data = $obj->getData();
    return $data["props"];
  }

  /**
   * Retrieve an array of all keys in this bucket.
   * Note: this operation is pretty slow.
   * @TODO: connect it to a callback so each key is passed to the callback instead
   * so it can do streaming I/O
   * @return Array
   */
  function getKeys( $cb = NULL ) {
    $response = $this->client->transport->getBucketKeys($this->getName());
    
    # Use a Riak\Object to interpret the response, we are just interested in the value.
    $obj = new Object($this, NULL);
    $obj->populate($response);
    if (!$obj->exists()) {
      throw Exception("Error getting bucket properties.");
    }
    $keys = $obj->getData();
    return array_map("urldecode",$keys["keys"]);
        
  }
  
  /**
   * Search a secondary index
   * @author Eric Stevens <estevens@taglabsinc.com>
   * @param string $indexName - The name of the index to search
   * @param string $indexType - The type of index ('int' or 'bin')
   * @param string|int $startOrExact
   * @param string|int optional $end
   * @param bool $dedupe - whether to eliminate duplicate entries if any
   * @return array of Riak\Links
   */
  function indexSearch($indexName, $indexType, $startOrExact, $end=NULL, $dedupe=false) {
    $response = $this->client->transport->indexSearch($this->getName(), $indexName, $indexType, $startOrExact, $end, $dedupe);
    $obj = new Object($this, NULL);
    $obj->populate($response);
    if (!$obj->exists()) {
      throw Exception("Error searching index.");
    }
    $data = $obj->getData();
    $keys = array_map("urldecode",$data["keys"]);
    
    $seenKeys = array();
    foreach($keys as $id=>&$key) {
      if ($dedupe) {
        if (isset($seenKeys[$key])) {
          unset($keys[$id]);
          continue;
        }
        $seenKeys[$key] = true;
      }
      $key = new Link($this->name, $key);
      $key->client = $this->client;
    }
    return $keys;
  }
  
    
  /**
  * access protected properties of the object ... just can't set them.
  */
  public function __get( $k ){
    return $this->$k;
  }

}

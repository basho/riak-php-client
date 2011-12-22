<?php
namespace Riak;

/**
 * The Riak\Object holds meta information about a Riak object, plus the
 * object's data.
 * @package Riak\Object
 */
class Object {

  protected $bucket;
  protected $key;
  protected $vclock;
  protected $links = array();
  protected $siblings = NULL;
  protected $exists = FALSE;
  protected $meta = array();
  protected $indexes = array();
  protected $autoIndexes = array();
  protected $content_type = NULL;
  public $jsonize = TRUE;


  /**
   * Construct a new Riak\Object.
   * @param Riak\Client $client - A Riak\Client object.
   * @param Riak\Bucket $bucket - A Riak\Bucket object.
   * @param string $key - An optional key. If not specified, then key
   * is generated by server when store(...) is called.
   */
  function __construct($bucket, $key=NULL) {
    $this->bucket = $bucket;
    $this->key = $key;
  }

  /**
   * Get the bucket of this object.
   * @return Riak\Bucket
   */
  function getBucket() {
    return $this->bucket;
  }

  /**
   * Get the key of this object.
   * @return string
   */
  function getKey() {
    return $this->key;
  }

  /**
   * Get the data stored in this object. Will return a associative
   * array, unless the object was constructed with newBinary(...) or
   * getBinary(...), in which case this will return a string.
   * @return array or string
   */
  function getData() { 
    return $this->data; 
  }

  /**
   * Set the data stored in this object. This data will be
   * JSON encoded unless the object was constructed with
   * newBinary(...) or getBinary(...).
   * @param mixed $data - The data to store.
   * @return $data
   */
  function setData($data) { 
    $this->data = $data; 
    return $this->data;
  }

  /**
   * Return true if the object exists, false otherwise. Allows you to
   * detect a get(...) or getBinary(...) operation where the object is missing.
   * @return boolean
   */
  function exists() {
    return $this->exists;
  }

  /**
   * Get the content type of this object. This is either text/json, or
   * the provided content type if the object was created via newBinary(...).
   * @return string
   */
  function getContentType() { 
    return $this->content_type;
  }

  /**
   * Set the content type of this object.
   * @param  string $content_type - The new content type.
   * @return $this
   */
  function setContentType($content_type) {
    $this->content_type = $content_type;
    return $this;
  }

  /**
   * Add a link to a Riak\Object.
   * @param mixed $obj - Either a Riak\Object or a Riak\Link object.
   * @param string $tag - Optional link tag. (default is bucket name,
   * ignored if $obj is a Riak\Link object.)
   * @return Riak\Object
   */
  function addLink($obj, $tag=NULL) {
    if ($obj instanceof Link)
      $newlink = $obj;
    else
      $newlink = new Link($obj->bucket->name, $obj->key, $tag);
   
    $this->removeLink($newlink);
    $this->links[] = $newlink;

    return $this;
  }
  
  /**
   * Remove a link to a Riak\Object.
   * @param mixed $obj - Either a Riak\Object or a Riak\Link object.
   * @param string $tag - 
   * @param mixed $obj - Either a Riak\Object or a Riak\Link object.
   * @param string $tag - Optional link tag. (default is bucket name,
   * ignored if $obj is a Riak\Link object.)
   * @return $this
   */
  function removeLink($obj, $tag=NULL) {
    if ($obj instanceof Link)
      $oldlink = $obj;
    else 
      $oldlink = new Link($obj->bucket->name, $obj->key, $tag);

    $a = array();
    foreach ($this->links as $link) {
      if (!$link->isEqual($oldlink)) 
        $a[] = $link;
    }

    $this->links = $a;
    return $this;
  }

  /**
   * Return an array of Riak\Link objects.
   * @return array()
   */
  function getLinks() {
    # Set the clients before returning...
    foreach ($this->links as $link) {
      $link->client = $this->bucket->client;
    }
    return $this->links;
  }
  
  /** @section Indexes */
  
  /**
   * Adds a secondary index to the object
   * This will create the index if it does not exist, or will
   * append an additional value if the index already exists and
   * does not contain the provided value.
   * @param string $indexName
   * @param string $indexType - Must be one of 'int' or 'bin' - the
   * only two index types supported by Riak
   * @param string|int optional $explicitValue - If provided, uses this
   * value explicitly.  If not provided, this will search the object's
   * data for a field with the name $indexName, and use that value.
   * @return $this
   */
  function addIndex($indexName, $indexType=null, $explicitValue = null) {
    if ($explicitValue === null) {
      $this->addAutoIndex($indexName, $indexType);
      return;
    }
    
    if ($indexType !== null) {
      $index = strtolower("{$indexName}_{$indexType}");
    } else {
      $index = strtolower($indexName);
    }
    if (!isset($this->indexes[$index])) $this->indexes[$index] = array();
    
    if (false === array_search($explicitValue, $this->indexes[$index])) {
      $this->indexes[$index][] = $explicitValue;
    }
    return $this;
  }
  
  /**
   * Sets a given index to a specific value or set of values
   * @param string $indexName
   * @param string $indexType - must be 'bin' or 'int'
   * @param array|string|int $values
   * @return $this
   */
  function setIndex($indexName, $indexType=null, $values) {
    if ($indexType !== null) {
      $index = strtolower("{$indexName}_{$indexType}");
    } else {
      $index = strtolower($indexName);
    }
    
    $this->indexes[$index] = $values;
    
    return $this;
  }
  
  /**
   * Gets the current values for the identified index
   * Note, the NULL value has special meaning - when the object is
   * ->store()d, this value will be replaced with the current value
   * the value of the field matching $indexName from the object's data
   * @param string $indexName
   * @param string $indexType
   */
  function getIndex($indexName, $indexType=null) {
    if ($indexType !== null) {
      $index = strtolower("{$indexName}_{$indexType}");
    } else {
      $index = strtolower($indexName);
    }
    if (!isset($this->indexes[$index])) return array();
    
    return $this->indexes[$index];
  }
  
  /**
   * Removes a specific value from a given index
   * @param string $indexName
   * @param string $indexType - must be 'bin' or 'int'
   * @param string|int optional $explicitValue
   * @return $this
   */
  function removeIndex($indexName, $indexType=null, $explicitValue = null) {
    if ($explicitValue === null) {
      $this->removeAutoIndex($indexName, $indexType);
      return;
    }
    if ($indexType !== null) {
      $index = strtolower("{$indexName}_{$indexType}");
    } else {
      $index = strtolower($indexName);
    }
    
    if (!isset($this->indexes[$index])) return;
    
    if (false !== ($position = array_search($explicitValue, $this->indexes[$index]))) {
      unset($this->indexes[$index][$position]);
    }
    
    return $this;
  }
  
  /**
   * Bulk index removal
   * If $indexName and $indexType are provided, all values for the
   * identified index are removed.
   * If just $indexName is provided, all values for all types of
   * the identified index are removed
   * If neither is provided, all indexes are removed from the object
   *
   * Note that this function will NOT affect auto indexes
   *
   * @param string optional $indexName
   * @param string optional $indexType
   *
   * @return $this
   */
  function removeAllIndexes($indexName=null, $indexType=null) {
    if ($indexName === null) {
      $this->indexes = array();
    } else if ($indexType === null) {
      $indexName = strtolower($indexName);
      unset($this->indexes["{$indexName}_int"]);
      unset($this->indexes["{$indexName}_bin"]);
    } else {
      unset($this->indexes[strtolower("{$indexName}_{$indexType}")]);
    }
    
    return $this;
  }

  /** @section Auto Indexes */
  
  /**
   * Adds an automatic secondary index to the object
   * The value of an automatic secondary index is determined at
   * time of ->store() by looking for an $fieldName key
   * in the object's data.
   *
   * @param string $fieldName
   * @param string $indexType Must be one of 'int' or 'bin'
   *
   * @return $this
   */
  function addAutoIndex($fieldName, $indexType=null) {
    if ($indexType !== null) {
      $index = strtolower("{$fieldName}_{$indexType}");
    } else {
      $index = strtolower($fieldName);
    }
    $this->autoIndexes[$index] = $fieldName;
    
    return $this;
  }
  
  /**
   * Returns whether the object has a given auto index
   * @param string $fieldName
   * @param string $indexType - must be one of 'int' or 'bin'
   *
   * @return boolean
   */
  function hasAutoIndex($fieldName, $indexType=null) {
    if ($indexType !== null) {
      $index = strtolower("{$fieldName}_{$indexType}");
    } else {
      $index = strtolower($fieldName);
    }
    return isset($this->autoIndexes[$index]);
  }
  
  /**
   * Removes a given auto index from the object
   *
   * @param string $fieldName
   * @param string $indexType
   *
   * @return $this
   */
  function removeAutoIndex($fieldName, $indexType=null) {
    if ($indexType !== null) {
      $index = strtolower("{$fieldName}_{$indexType}");
    } else {
      $index = strtolower($fieldName);
    }
    unset($this->autoIndexes[$index]);
    return $this;
  }
  
  /**
   * Removes all auto indexes
   * If $fieldName is not provided, all auto indexes on the
   * object are stripped, otherwise just indexes on the given field
   * are stripped.
   * If $indexType is not provided, all types of index for the
   * given field are stripped, otherwise just a given type is stripped.
   *
   * @param string $fieldName
   * @param string $indexType
   *
   * @return $this
   */
  function removeAllAutoIndexes($fieldName = null, $indexType = null) {
    if ($fieldName === null) {
      $this->autoIndexes = array();
    } else if ($indexType === null) {
      $fieldName = strtolower($fieldName);
      unset($this->autoIndexes["{$fieldName}_bin"]);
      unset($this->autoIndexes["{$fieldName}_int"]);
    } else {
      unset($this->autoIndexes[strtolower("{$fieldName}_{$indexType}")]);
    }
  }
  
  /** @section Meta Data */
  
  /**
   * Gets a given metadata value
   * Returns null if no metadata value with the given name exists
   *
   * @param string $metaName
   *
   * @return string|null
   */
  function getMeta($metaName) {
    $metaName = strtolower($metaName);
    if (isset($this->meta[$metaName])) return $this->meta[$metaName];
    return null;
  }
  
  /**
   * Sets a given metadata value, overwriting an existing
   * value with the same name if it exists.
   * @param string $metaName
   * @param string $value
   * @return $this
   */
  function setMeta($metaName, $value) {
    $this->meta[strtolower($metaName)] = $value;
    return $this;
  }
  
  /**
   * Removes a given metadata value
   * @param string $metaName
   * @return $this
   */
  function removeMeta($metaName) {
    unset ($this->meta[strtolower($metaName)]);
    return $this;
  }
  
  /**
   * Gets all metadata values
   * @return array<string>=string
   */
  function getAllMeta() {
    return $this->meta;
  }
  
  /**
   * Strips all metadata values
   * @return $this;
   */
  function removeAllMeta() {
    $this->meta = array();
    return $this;
  }

  /**
   * Store the object in Riak. When this operation completes, the
   * object could contain new metadata and possibly new data if Riak
   * contains a newer version of the object according to the object's
   * vector clock.  
   * @param integer $w - W-value, wait for this many partitions to respond
   * before returning to client.
   * @param integer $dw - DW-value, wait for this many partitions to
   * confirm the write before returning to client.
   * @return $this
   */
  function store($w=NULL, $dw=NULL) {
    # Use defaults if not specified...
    $w = $this->bucket->getW($w);
    $dw = $this->bucket->getDW($w);
    $response = $this->bucket->client->transport->put($this, $w, $dw);
    $this->populate( $response );
    return $this;
  }
 
  /**
   * Reload the object from Riak. When this operation completes, the
   * object could contain new metadata and a new value, if the object
   * was updated in Riak since it was last retrieved.
   * @param integer $r - R-Value, wait for this many partitions to respond
   * before returning to client.
   * @return $this
   */
  function reload($r=NULL) {
    # Do the request...
    $r = $this->bucket->getR($r);
    $response = $this->bucket->client->transport->get( $this, $r );
    $this->populate($response);
    
    # If there are siblings, load the data for the first one by default...
    if ($this->hasSiblings()) {
      $obj = $this->getSibling(0);
      $this->setData($obj->getData());
    }
    return $this;
  }

  /**
   * Delete this object from Riak.
   * @param  integer $dw - DW-value. Wait until this many partitions have
   * deleted the object before responding.
   * @return $this
   */
  function delete($dw=NULL) {
    # Use defaults if not specified...
    $dw = $this->bucket->getDW($dw);

    $response = $this->bucket->client->transport->delete( $this, $dw );    
    $this->populate($response);

    return $this;
  }


  /**
   * Reset this object.
   * @return $this
   */
  private function clear() {
      $this->links = array();
      $this->data = NULL;
      $this->exists = FALSE;
      $this->siblings = NULL;
      $this->indexes = array();
      $this->autoIndexes = array();
      $this->meta = array();
      return $this;
  }

  /**
   * Get the vclock of this object.
   * @return string
   */
  private function vclock() {
    return $this->vclock;
  }

  /**
   * Given the output of the transport populate the object. Only for use by the Riak client
   * library.
   * @return $this
   */
  function populate($response) {
    $this->clear();

    # If no response given, then return.    
    if ($response == NULL) {
      return $this;
    }
      
    # If we are here, then the object exists...
    $this->exists = TRUE;

    //foreach( $response as $k => $v ) $this->$k = $v;
    $this->data = ( $this->jsonize && is_scalar( $response->data ) ) ? json_decode( $response->data, TRUE ) : $response->data;
    $this->links = $response->links;
    $this->indexes = $response->indexes;
    $this->meta = $response->meta;
    $this->vclock = $response->vclock;
    $this->siblings = $response->siblings;
    $this->content_type = $response->content_type;
    if( $response->key !== NULL ) $this->key = $response->key;
    
    # Look for auto indexes and deindex explicit values if appropriate
    if (isset($this->meta['x-rc-autoindex'])) {
      # dereference the autoindexes
      $this->autoIndexes = json_decode($this->meta['x-rc-autoindex'], true);
      $collisions = isset($this->meta['x-rc-autoindexcollision']) ? json_decode($this->meta['x-rc-autoindexcollision'], true) : array();
      
      foreach($this->autoIndexes as $index=>$fieldName) {
        $value = null;
        if (isset($this->data[$fieldName])) {
          $value = $this->data[$fieldName];
          if (isset($collisions[$index]) && $collisions[$index] === $value) {
            // Don't strip this value, it's an explicit index.
          } else {
            if ($value !== null) $this->removeIndex($index, null, $value);
          }
        }
        
        if (!isset($collisions[$index])) {
          // Do not delete this value if
        }
      }
    }
    
    return $this;
  }

  /**
   * Return true if this object has siblings.
   * @return boolean
   */
  function hasSiblings() {
    return ($this->getSiblingCount() > 0);
  }

  /**
   * Get the number of siblings that this object contains.
   * @return integer
   */
  function getSiblingCount() {
    return count($this->siblings);
  }

  /**
   * Retrieve a sibling by sibling number.
   * @param  integer $i - Sibling number.
   * @param  integer $r - R-Value. Wait until this many partitions
   * have responded before returning to client.
   * @return Object.
   */
  function getSibling($i, $r=NULL) {
    # Use defaults if not specified.
    $r = $this->bucket->getR($r);

    # Run the request...
    $vtag = $this->siblings[$i];
    $r = $this->bucket->getR($r);
    $response = $this->bucket->client->transport->get( $this, $r, $vtag );

    # Respond with a new instance...
    $obj = new self($this->bucket, $this->key);
    $obj->jsonize = $this->jsonize;
    $obj->populate($response);
    return $obj;
  }

  /**
   * Retrieve an array of siblings.
   * @param integer $r - R-Value. Wait until this many partitions have
   * responded before returning to client.
   * @return array of Riak\Object
   */
  function getSiblings($r=NULL) {
    $a = array();
    for ($i = 0; $i<$this->getSiblingCount(); $i++) {
      $a[] = $this->getSibling($i, $r);
    }
    return $a;
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see Riak\MapReduce::add()
   * @return Riak\MapReduce
   */
  function add($params) {
    $mr = new MapReduce($this->bucket->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "add"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see Riak\MapReduce::link()
   * @return Riak\MapReduce
   */
  function link($params) {
    $mr = new MapReduce($this->bucket->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "link"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see Riak\MapReduce::map()
   * @return Riak\MapReduce
   */
  function map($params) {
    $mr = new MapReduce($this->bucket->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "map"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see Riak\MapReduce::reduce()
   * @return Riak\MapReduce
   */
  function reduce($params) {
    $mr = new MapReduce($this->bucket->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "reduce"), $args);
  }
  
  /**
  * access protected properties of the object ... just can't set them.
  */
  public function __get( $k ){
    return $this->$k;
  }

}
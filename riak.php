<?php

/* 
   This file is provided to you under the Apache License,
   Version 2.0 (the "License"); you may not use this file
   except in compliance with the License.  You may obtain
   a copy of the License at
   
   http://www.apache.org/licenses/LICENSE-2.0
   
   Unless required by applicable law or agreed to in writing,
   software distributed under the License is distributed on an
   "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
   KIND, either express or implied.  See the License for the
   specific language governing permissions and limitations
   under the License.    
*/


/**
 * The Riak API for PHP allows you to connect to a Riak instance,
 * create, modify, and delete Riak objects, add and remove links from
 * Riak objects, run Javascript (and
 * Erlang) based Map/Reduce operations, and run Linkwalking
 * operations.
 *
 * See the unit_tests.php file for example usage.
 * 
 * @author Rusty Klophaus (@rklophaus) (rusty@basho.com)
 * @package RiakAPI
 */

/**
 * The RiakClient object holds information necessary to connect to
 * Riak. The Riak API uses HTTP, so there is no persistent
 * connection, and the RiakClient object is extremely lightweight.
 * @package RiakClient
 */
class RiakClient {
  /**
   * Construct a new RiakClient object.
   * @param string $host - Hostname or IP address (default '127.0.0.1')
   * @param int $port - Port number (default 8098)
   * @param string $prefix - Interface prefix (default "riak")
   * @param string $mapred_prefix - MapReduce prefix (default "mapred")
   */
  function RiakClient($host='127.0.0.1', $port=8098, $prefix='riak', $mapred_prefix='mapred') {
    $this->host = $host;
    $this->port = $port;
    $this->prefix = $prefix;    
    $this->mapred_prefix = $mapred_prefix;
    $this->indexPrefix='buckets';
    $this->clientid = 'php_' . base_convert(mt_rand(), 10, 36);
    $this->r = 2;
    $this->w = 2;
    $this->dw = 2;
  }

  /**
   * Get the R-value setting for this RiakClient. (default 2)
   * @return integer
   */
  function getR() { 
    return $this->r; 
  }

  /**
   * Set the R-value for this RiakClient. This value will be used
   * for any calls to get(...) or getBinary(...) where where 1) no
   * R-value is specified in the method call and 2) no R-value has
   * been set in the RiakBucket.  
   * @param integer $r - The R value.
   * @return $this
   */
  function setR($r) { 
    $this->r = $r; 
    return $this; 
  }

  /**
   * Get the W-value setting for this RiakClient. (default 2)
   * @return integer
   */
  function getW() { 
    return $this->w; 
  }

  /**
   * Set the W-value for this RiakClient. See setR(...) for a
   * description of how these values are used.
   * @param integer $w - The W value.
   * @return $this
   */
  function setW($w) { 
    $this->w = $w; 
    return $this; 
  }

  /**
   * Get the DW-value for this ClientOBject. (default 2)
   * @return integer
   */
  function getDW() { 
    return $this->dw; 
  }

  /**
   * Set the DW-value for this RiakClient. See setR(...) for a
   * description of how these values are used.
   * @param  integer $dw - The DW value.
   * @return $this
   */
  function setDW($dw) { 
    $this->dw = $dw; 
    return $this; 
  }

  /**
   * Get the clientID for this RiakClient.
   * @return string
   */
  function getClientID() { 
    return $this->clientid; 
  }

  /**
   * Set the clientID for this RiakClient. Should not be called
   * unless you know what you are doing.
   * @param string $clientID - The new clientID.
   * @return $this
   */
  function setClientID($clientid) { 
    $this->clientid = $clientid; 
    return $this;
  }

  /**
   * Get the bucket by the specified name. Since buckets always exist,
   * this will always return a RiakBucket.
   * @return RiakBucket
   */
  function bucket($name) {
    return new RiakBucket($this, $name);
  }

  /**
   * Get all buckets.
   * @return array() of RiakBucket objects
   */
  function buckets() {
    $url = RiakUtils::buildRestPath($this);
    $response = RiakUtils::httpRequest('GET', $url.'?buckets=true');
    $response_obj = json_decode($response[1]);
    $buckets = array();
    foreach($response_obj->buckets as $name) {
        $buckets[] = $this->bucket($name);
    }
    return $buckets;
  }

  /**
   * Check if the Riak server for this RiakClient is alive.
   * @return boolean
   */
  function isAlive() {
    $url = 'http://' . $this->host . ':' . $this->port . '/ping';
    $response = RiakUtils::httpRequest('GET', $url);
    return ($response != NULL) && ($response[1] == 'OK');
  }


  # MAP/REDUCE/LINK FUNCTIONS

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::add()
   * @return RiakMapReduce
   */
  function add($params) {
    $mr = new RiakMapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "add"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation. This command will 
   * return an error unless executed against a Riak Search cluster.
   * @see RiakMapReduce::search()
   * @return RiakMapReduce
   */
  function search($params) {
    $mr = new RiakMapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "search"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::link()
   */
  function link($params) {
    $mr = new RiakMapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "link"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::map()
   */
  function map($params) {
    $mr = new RiakMapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "map"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::reduce()
   */
  function reduce($params) {
    $mr = new RiakMapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "reduce"), $args);
  }
}


/**
 * The RiakMapReduce object allows you to build up and run a
 * map/reduce operation on Riak.
 * @package RiakMapReduce
 */
class RiakMapReduce {
  
  /**
   * Construct a Map/Reduce object.
   * @param RiakClient $client - A RiakClient object.
   * @return RiakMapReduce
   */
  function RiakMapReduce($client) {
    $this->client = $client;
    $this->phases = array();
    $this->inputs = array();
    $this->input_mode = NULL;
    $this->key_filters = array();
  }

  /**
   * Add inputs to a map/reduce operation. This method takes three
   * different forms, depending on the provided inputs. You can
   * specify either  a RiakObject, a string bucket name, or a bucket,
   * key, and additional arg.
   * @param mixed $arg1 - RiakObject or Bucket
   * @param mixed $arg2 - Key or blank
   * @param mixed $arg3 - Arg or blank
   * @return RiakMapReduce
   */
  function add($arg1, $arg2=NULL, $arg3=NULL) {
    if (func_num_args() == 1) {
      if ($arg1 instanceof RiakObject) 
        return $this->add_object($arg1);
      else
        return $this->add_bucket($arg1);
    }
    return $this->add_bucket_key_data($arg1, (string) $arg2, $arg3);
  }

  /**
   * Private.
   */
  private function add_object($obj) {
    return $this->add_bucket_key_data($obj->bucket->name, $obj->key, NULL);
  }
  
  /**
   * Private.
   */
  private function add_bucket_key_data($bucket, $key, $data) {
    if ($this->input_mode == "bucket") 
      throw new Exception("Already added a bucket, can't add an object.");
    $this->inputs[] = array($bucket, $key, $data);
    return $this;
  }

  /**
   * Private.
   * @return $this
   */
  private function add_bucket($bucket) {
    $this->input_mode = "bucket";
    $this->inputs = $bucket;
    return $this;
  }

  /**
   * Begin a map/reduce operation using a Search. This command will 
   * return an error unless executed against a Riak Search cluster.
   * @param string $bucket - The Bucket to search.  @param string
   * query - The Query to execute. (Lucene syntax.)  @return
   * RiakMapReduce
   */
  function search($bucket, $query) {
    $this->inputs = array("module"=>"riak_search", "function"=>"mapred_search", "arg"=>array($bucket, $query));
    return $this;
  }

  /**
   * Add a link phase to the map/reduce operation.
   * @param string $bucket - Bucket name (default '_', which means all
   * buckets)
   * @param string $tag - Tag (default '_', which means all buckets)
   * @param boolean $keep - Flag whether to keep results from this
   * stage in the map/reduce. (default FALSE, unless this is the last
   * step in the phase)
   * @return $this
   */
  function link($bucket='_', $tag='_', $keep=FALSE) {
    $this->phases[] = new RiakLinkPhase($bucket, $tag, $keep);
    return $this;
  }

  /**
   * Add a map phase to the map/reduce operation.
   * @param mixed $function - Either a named Javascript function (ie:
   * "Riak.mapValues"), or an anonymous javascript function (ie:
   * "function(...) { ... }" or an array ["erlang_module",
   * "function"].
   * @param array() $options - An optional associative array
   * containing "language", "keep" flag, and/or "arg".
   * @return $this
   */
  function map($function, $options=array()) {
    $language = is_array($function) ? "erlang" : "javascript";
    $this->phases[] = new RiakMapReducePhase("map",
                                             $function,
                                             RiakUtils::get_value("language", $options, $language),
                                             RiakUtils::get_value("keep", $options, FALSE),
                                             RiakUtils::get_value("arg", $options, NULL));
    return $this;
  }

  /**
   * Add a reduce phase to the map/reduce operation.
   * @param mixed $function - Either a named Javascript function (ie:
   * "Riak.mapValues"), or an anonymous javascript function (ie:
   * "function(...) { ... }" or an array ["erlang_module",
   * "function"].
   * @param array() $options - An optional associative array
   * containing "language", "keep" flag, and/or "arg".
   * @return $this
   */
  function reduce($function, $options=array()) {
    $language = is_array($function) ? "erlang" : "javascript";
    $this->phases[] = new RiakMapReducePhase("reduce", 
                                             $function,
                                             RiakUtils::get_value("language", $options, $language),
                                             RiakUtils::get_value("keep", $options, FALSE),
                                             RiakUtils::get_value("arg", $options, NULL));
    return $this;
  }
  
  /**
   * Add a key filter to the map/reduce operation.  If there are already
   * existing filters, an "and" condition will be used to combine them.
   * Alias for key_filter_and
   * @param array $filter - a key filter (ie: 
   * ->key_filter(
   * 	array("tokenize", "-", 2), 
   * 	array("between", "20110601", "20110630")
   * )
   * @return $this
   */
  function key_filter(array $filter /*. ,$filter .*/) {
    $args = func_get_args();
    array_unshift($args, 'and');
    return call_user_func_array(array($this, 'key_filter_operator'), $args);
  }
  
  /**
   * Add a key filter to the map/reduce operation.  If there are already
   * existing filters, an "and" condition will be used to combine them.
   * @param array $filter - a key filter (ie: 
   * ->key_filter(
   * 	array("tokenize", "-", 2), 
   * 	array("between", "20110601", "20110630")
   * )
   * @return $this
   */
  function key_filter_and(array $filter) {
    $args = func_get_args();
    array_unshift($args, 'and');
    return call_user_func_array(array($this, 'key_filter_operator'), $args);
  }
  
  /**
   * Adds a key filter to the map/reduce operation.  If there are already
   * existing filters, an "or" condition will be used to combine with the
   * existing filters.
   * @param array $filter
   * @return $this
   */
  function key_filter_or(array $filter /*. ,$filter .*/) {
    $args = func_get_args();
    array_unshift($args, 'or');
    return call_user_func_array(array($this, 'key_filter_operator'), $args);
  }
  
  /**
   * Adds a key filter to the map/reduce operation.  If there are already
   * existing filters, the provided conditional operator will be used
   * to combine with the existing filters.
   * @param string $operator - Operator (usually "and" or "or")
   * @param array $filter
   * @return $this
   */
  function key_filter_operator($operator,  $filter /*. ,$filter .*/) {
    $filters = func_get_args();
    array_shift($filters);
    if ($this->input_mode != 'bucket') 
  	  throw new Exception("Key filters can only be used in bucket mode");
    
  	if (count($this->key_filters) > 0) {
  	  $this->key_filters = array(array(
  	    $operator,
  	    $this->key_filters,
  	    $filters
  	  ));
  	} else {
  		$this->key_filters = $filters;
  	}
  	return $this;
  }
  
  /**
   * Run the map/reduce operation. Returns an array of results, or an
   * array of RiakLink objects if the last phase is a link phase. 
   * @param integer $timeout - Timeout in seconds.
   * @return array()
   */
  function run($timeout=NULL) {
    $num_phases = count($this->phases);

    $linkResultsFlag = FALSE;

    # If there are no phases, then just echo the inputs back to the user.
    if ($num_phases == 0) {
      $this->reduce(array("riak_kv_mapreduce", "reduce_identity"));
      $num_phases = 1;
      $linkResultsFlag = TRUE;
    }

    # Convert all phases to associative arrays. Also,
    # if none of the phases are accumulating, then set the last one to
    # accumulate.
    $keep_flag = FALSE;
    $query = array();
    for ($i = 0; $i < $num_phases; $i++) {
      $phase = $this->phases[$i];
      if ($i == ($num_phases - 1) && !$keep_flag)
        $phase->keep = TRUE;
      if ($phase->keep) $keep_flag = TRUE;
      $query[] = $phase->to_array();
    }
    
    # Add key filters if applicable
   	if ($this->input_mode == 'bucket' && count($this->key_filters) > 0) {
   		$this->inputs = array(
   			'bucket' => $this->inputs,
   			'key_filters' => $this->key_filters
   		);
   	}

    # Construct the job, optionally set the timeout...
    $job = array("inputs"=>$this->inputs, "query"=>$query);
    if ($timeout != NULL) $job["timeout"] = $timeout;
    $content = json_encode($job); 
    
    # Do the request...
    $url = "http://" . $this->client->host . ":" . $this->client->port . "/" . $this->client->mapred_prefix;
    $response = RiakUtils::httpRequest('POST', $url, array(), $content);
    $result = json_decode($response[1]);

    # If the last phase is NOT a link phase, then return the result.
    $linkResultsFlag |= (end($this->phases) instanceof RiakLinkPhase);

    # If we don't need to link results, then just return.
    if (!$linkResultsFlag) return $result;

    # Otherwise, if the last phase IS a link phase, then convert the
    # results to RiakLink objects.
    $a = array();
    foreach ($result as $r) {
      $tag = isset($r[2]) ? $r[2] : null;
      $link = new RiakLink($r[0], $r[1], $tag);
      $link->client = $this->client;
      $a[] = $link;
    }
    return $a;
  }
}

/**
 * The RiakMapReducePhase holds information about a Map phase or
 * Reduce phase in a RiakMapReduce operation.
 * @package RiakMapReducePhase
 */
class RiakMapReducePhase {
  /**
   * Construct a RiakMapReducePhase object.
   * @param string $type - "map" or "reduce"
   * @param mixed $function - string or array() 
   * @param string $language - "javascript" or "erlang"
   * @param boolean $keep - True to return the output of this phase in
   * the results.
   * @param mixed $arg - Additional value to pass into the map or
   * reduce function.
   */
  function RiakMapReducePhase($type, $function, $language, $keep, $arg) {
    $this->type = $type;
    $this->language = $language;
    $this->function = $function;
    $this->keep = $keep;
    $this->arg = $arg;
  }
  
  /**
   * Convert the RiakMapReducePhase to an associative array. Used
   * internally.
   */
  function to_array() {
    $stepdef = array("keep"=>$this->keep,
                     "language"=>$this->language,
                     "arg"=>$this->arg);

    if ($this->language == "javascript" && is_array($this->function)) {
      $stepdef["bucket"] = $this->function[0];      
      $stepdef["key"] = $this->function[1];      
    } else if ($this->language == "javascript" && is_string($this->function)) {
      if (strpos($this->function, "{") == FALSE) 
        $stepdef["name"] = $this->function;      
      else
        $stepdef["source"] = $this->function;      
    } else if ($this->language == "erlang" && is_array($this->function)) {
      $stepdef["module"] = $this->function[0];
      $stepdef["function"] = $this->function[1];
    }
    
    return array(($this->type)=>$stepdef);
  }
}

/**
 * The RiakLinkPhase object holds information about a Link phase in a
 * map/reduce operation.
 * @package RiakLinkPhase
 */
class RiakLinkPhase {
  /**
   * Construct a RiakLinkPhase object.
   * @param string $bucket - The bucket name.
   * @param string $tag - The tag.
   * @param boolean $keep - True to return results of this phase.
   */
  function RiakLinkPhase($bucket, $tag, $keep) {
    $this->bucket = $bucket;
    $this->tag = $tag;
    $this->keep = $keep;
  }

  /**
   * Convert the RiakLinkPhase to an associative array. Used
   * internally.
   */
  function to_array() {
    $stepdef = array("bucket"=>$this->bucket,
                     "tag"=>$this->tag,
                     "keep"=>$this->keep);
    return array("link"=>$stepdef);
  }
}

/**
 * The RiakLink object represents a link from one Riak object to
 * another.
 * @package RiakLink
 */
class RiakLink {
  /**
   * Construct a RiakLink object.
   * @param string $bucket - The bucket name.
   * @param string $key - The key.
   * @param string $tag - The tag.
   */
  function RiakLink($bucket, $key, $tag=NULL) {
    $this->bucket = $bucket;
    $this->key = $key;
    $this->tag = $tag;
    $this->client = NULL;
  }

  /**
   * Retrieve the RiakObject to which this link points.
   * @param integer $r - The R-value to use.
   * @return RiakObject
   */
  function get($r=NULL) {
    return $this->client->bucket($this->bucket)->get($this->key, $r);
  }

  /**
   * Retrieve the RiakObject to which this link points, as a binary.
   * @param integer $r - The R-value to use.
   * @return RiakObject
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
   * Convert this RiakLink object to a link header string. Used internally.
   */
  function toLinkHeader($client) {
    $link = "</" .
      $client->prefix . "/" .
      urlencode($this->bucket) . "/" .
      urlencode($this->key) . ">; riaktag=\"" . 
      urlencode($this->getTag()) . "\"";
    return $link;
  }
  
  /**
   * Return true if the links are equal.
   * @param RiakLink $link - A RiakLink object.
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



/**
 * The RiakBucket object allows you to access and change information
 * about a Riak bucket, and provides methods to create or retrieve
 * objects within the bucket.
 * @package RiakBucket
 */
class RiakBucket {
  function RiakBucket($client, $name) {
    $this->client = $client;
    $this->name = $name;
    $this->r = NULL;
    $this->w = NULL;
    $this->dw = NULL;
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
  function getW($w)     { 
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
  function getDW($dw)    { 
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
   * Create a new Riak object that will be stored as JSON.
   * @param  string $key - Name of the key.
   * @param  object $data - The data to store. (default NULL)
   * @return RiakObject
   */
  function newObject($key, $data=NULL) {
    $obj = new RiakObject($this->client, $this, $key);
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
    $obj = new RiakObject($this->client, $this, $key);
    $obj->setData($data);
    $obj->setContentType($content_type);
    $obj->jsonize = FALSE;
    return $obj;
  }

  /**
   * Retrieve a JSON-encoded object from Riak.
   * @param  string $key - Name of the key.
   * @param  int    $r   - R-Value of the request (defaults to bucket's R)
   * @return RiakObject
   */
  function get($key, $r=NULL) {
    $obj = new RiakObject($this->client, $this, $key);
    $obj->jsonize = TRUE;
    $r = $this->getR($r);
    return $obj->reload($r);
  }

  /**
   * Retrieve a binary/string object from Riak.
   * @param  string $key - Name of the key.
   * @param  int    $r   - R-Value of the request (defaults to bucket's R)
   * @return RiakObject
   */
  function getBinary($key, $r=NULL) {
    $obj = new RiakObject($this->client, $this, $key);
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
    $url = RiakUtils::buildRestPath($this->client, $this);
    $headers = array('Content-Type: application/json');
    $content = json_encode(array("props"=>$props));
    
    # Run the request...
    $response = RiakUtils::httpRequest('PUT', $url, $headers, $content);

    # Handle the response...
    if ($response == NULL) {
      throw Exception("Error setting bucket properties.");
    }
    
    # Check the response value...
    $status = $response[0]['http_code'];
    if ($status != 204) {
      throw Exception("Error setting bucket properties.");
    }
  }

  /**
   * Retrieve an associative array of all bucket properties.
   * @return Array
   */
  function getProperties() {
    # Run the request...
    $params = array('props' => 'true', 'keys' => 'false');
    $url = RiakUtils::buildRestPath($this->client, $this, NULL, NULL, $params);
    $response = RiakUtils::httpRequest('GET', $url);

    # Use a RiakObject to interpret the response, we are just interested in the value.
    $obj = new RiakObject($this->client, $this, NULL);
    $obj->populate($response, array(200));
    if (!$obj->exists()) {
      throw Exception("Error getting bucket properties.");
    }
    
    $props = $obj->getData();
    $props = $props["props"];

    return $props;
  }

  /**
   * Retrieve an array of all keys in this bucket.
   * Note: this operation is pretty slow.
   * @return Array
   */
  function getKeys() {
    $params = array('props'=>'false','keys'=>'true');
    $url = RiakUtils::buildRestPath($this->client, $this, NULL, NULL, $params);
    $response = RiakUtils::httpRequest('GET', $url);

    # Use a RiakObject to interpret the response, we are just interested in the value.
    $obj = new RiakObject($this->client, $this, NULL);
    $obj->populate($response, array(200));
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
   * @return array of RiakLinks
   */
  function indexSearch($indexName, $indexType, $startOrExact, $end=NULL, $dedupe=false) {
    $url = RiakUtils::buildIndexPath($this->client, $this, "{$indexName}_{$indexType}", $startOrExact, $end, NULL);
    $response = RiakUtils::httpRequest('GET', $url);
    
    $obj = new RiakObject($this->client, $this, NULL);
    $obj->populate($response, array(200));
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
      $key = new RiakLink($this->name, $key);
      $key->client = $this->client;
    }
    return $keys;
  }

}


/**
 * The RiakObject holds meta information about a Riak object, plus the
 * object's data.
 * @package RiakObject
 */
class RiakObject {

  protected $meta = array();
  protected $indexes = array();
  protected $autoIndexes = array();

  /**
   * Construct a new RiakObject.
   * @param RiakClient $client - A RiakClient object.
   * @param RiakBucket $bucket - A RiakBucket object.
   * @param string $key - An optional key. If not specified, then key
   * is generated by server when store(...) is called.
   */
  function RiakObject($client, $bucket, $key=NULL) {
    $this->client = $client;
    $this->bucket = $bucket;
    $this->key = $key;
    $this->jsonize = TRUE;
    $this->headers = array();
    $this->links = array();
    $this->siblings = NULL;
    $this->exists = FALSE;
  }

  /**
   * Get the bucket of this object.
   * @return RiakBucket
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
   * Get the HTTP status from the last operation on this object.
   * @return integer
   */
  function status() {
    return $this->headers['http_code'];
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
    return $this->headers['content-type']; 
  }

  /**
   * Set the content type of this object.
   * @param  string $content_type - The new content type.
   * @return $this
   */
  function setContentType($content_type) {
    $this->headers['content-type'] = $content_type;
    return $this;
  }

  /**
   * Add a link to a RiakObject.
   * @param mixed $obj - Either a RiakObject or a RiakLink object.
   * @param string $tag - Optional link tag. (default is bucket name,
   * ignored if $obj is a RiakLink object.)
   * @return RiakObject
   */
  function addLink($obj, $tag=NULL) {
    if ($obj instanceof RiakLink)
      $newlink = $obj;
    else
      $newlink = new RiakLink($obj->bucket->name, $obj->key, $tag);
   
    $this->removeLink($newlink);
    $this->links[] = $newlink;

    return $this;
  }
  
  /**
   * Remove a link to a RiakObject.
   * @param mixed $obj - Either a RiakObject or a RiakLink object.
   * @param string $tag - 
   * @param mixed $obj - Either a RiakObject or a RiakLink object.
   * @param string $tag - Optional link tag. (default is bucket name,
   * ignored if $obj is a RiakLink object.)
   * @return $this
   */
  function removeLink($obj, $tag=NULL) {
    if ($obj instanceof RiakLink)
      $oldlink = $obj;
    else 
      $oldlink = new RiakLink($obj->bucket->name, $obj->key, $tag);

    $a = array();
    foreach ($this->links as $link) {
      if (!$link->isEqual($oldlink)) 
        $a[] = $link;
    }

    $this->links = $a;
    return $this;
  }

  /**
   * Return an array of RiakLink objects.
   * @return array()
   */
  function getLinks() {
    # Set the clients before returning...
    foreach ($this->links as $link) {
      $link->client = $this->client;
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

    # Construct the URL...
    $params = array('returnbody' => 'true', 'w' => $w, 'dw' => $dw);
    $url = RiakUtils::buildRestPath($this->client, $this->bucket, $this->key, NULL, $params);
    
    # Construct the headers...
    $headers = array('Accept: text/plain, */*; q=0.5',
                     'Content-Type: ' . $this->getContentType(),
                     'X-Riak-ClientId: ' . $this->client->getClientID());

    # Add the vclock if it exists...
    if ($this->vclock() != NULL) {
      $headers[] = 'X-Riak-Vclock: ' . $this->vclock();
    }

    # Add the Links...
    foreach ($this->links as $link) {
      $headers[] = 'Link: ' . $link->toLinkHeader($this->client);
    }

    # Add the auto indexes...
    $collisions = array();
    foreach($this->autoIndexes as $index=>$fieldName) {
      $value = null;
      // look up the value
      if (isset($this->data[$fieldName])) {
        $value = $this->data[$fieldName];
        $headers[] = "x-riak-index-$index: ".urlencode($value);
        
        // look for value collisions with normal indexes
        if (isset($this->indexes[$index])) {
          if (false !== array_search($value, $this->indexes[$index])) {
            $collisions[$index] = $value;
          }
        }
      }
    }
    count($this->autoIndexes) > 0
      ? $this->meta['x-rc-autoindex'] = json_encode($this->autoIndexes)
      : $this->meta['x-rc-autoindex'] = null;
    count($collisions) > 0
      ? $this->meta['x-rc-autoindexcollision'] = json_encode($collisions)
      : $this->meta['x-rc-autoindexcollision'] = null;
    
    # Add the indexes
    foreach ($this->indexes as $index=>$values) {
      $headers[] = "x-riak-index-$index: " . join(', ', array_map('urlencode', $values));
    }
    
    
    # Add the metadata...
    foreach($this->meta as $metaName=>$metaValue) {
      if ($metaValue !== null) $headers[] = "X-Riak-Meta-$metaName: $metaValue";
    }

    if ($this->jsonize) {
      $content = json_encode($this->getData());
    } else {
      $content = $this->getData();
    }
  
    $method = $this->key ? 'PUT' : 'POST';

    # Run the operation.
    $response = RiakUtils::httpRequest($method, $url, $headers, $content);
    $this->populate($response, array(200, 201, 300));
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
    $params = array('r' => $r);
    $url = RiakUtils::buildRestPath($this->client, $this->bucket, $this->key, NULL, $params);
    $response = RiakUtils::httpRequest('GET', $url);
    $this->populate($response, array(200, 300, 404));
    
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

    # Construct the URL...
    $params = array('dw' => $dw);
    $url = RiakUtils::buildRestPath($this->client, $this->bucket, $this->key, NULL, $params);

    # Run the operation...
    $response = RiakUtils::httpRequest('DELETE', $url);    
    $this->populate($response, array(204, 404));

    return $this;
  }


  /**
   * Reset this object.
   * @return $this
   */
  private function clear() {
      $this->headers = array();
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
    if (array_key_exists('x-riak-vclock', $this->headers)) {
      return $this->headers['x-riak-vclock'];
    } else {
      return NULL;
    }
  }

  /**
   * Given the output of RiakUtils::httpRequest and a list of
   * statuses, populate the object. Only for use by the Riak client
   * library.
   * @return $this
   */
  function populate($response, $expected_statuses) {
    $this->clear();

    # If no response given, then return.    
    if ($response == NULL) {
      return $this;
    }
  
    # Update the object...
    $this->headers = $response[0];
    $this->data = $response[1];
    $status = $this->status();

    # Check if the server is down (status==0)
    if ($status == 0) {
      $m = 'Could not contact Riak Server: http://' . $this->client->host . ':' . $this->client->port . '!';
      throw new Exception($m);
    }

    # Verify that we got one of the expected statuses. Otherwise, throw an exception.
    if (!in_array($status, $expected_statuses)) {
      $m = 'Expected status ' . implode(' or ', $expected_statuses) . ', received ' . $status . ' with body: ' . $this->data;
      throw new Exception($m);
    }

    # If 404 (Not Found), then clear the object.
    if ($status == 404) {
      $this->clear();
      return $this;
    } 
      
    # If we are here, then the object exists...
    $this->exists = TRUE;

    # Parse the link header...
    if (array_key_exists("link", $this->headers)) {
      $this->populateLinks($this->headers["link"]);
    }

    # Parse the index and metadata headers
    $this->indexes = array();
    $this->autoIndexes = array();
    $this->meta = array();
    foreach($this->headers as $key=>$val) {
      if (preg_match('~^x-riak-([^-]+)-(.+)$~', $key, $matches)) {
        switch($matches[1]) {
          case 'index':
            $index = substr($matches[2], 0, strrpos($matches[2], '_'));
            $type = substr($matches[2], strlen($index)+1);
            $this->setIndex($index, $type, array_map('urldecode', explode(', ', $val)));
            break;
          case 'meta':
            $this->meta[$matches[2]] = $val;
            break;
        }
      }
    }

    # If 300 (Siblings), then load the first sibling, and
    # store the rest.
    if ($status == 300) {
      $siblings = explode("\n", trim($this->data));
      array_shift($siblings); # Get rid of 'Siblings:' string.
      $this->siblings = $siblings;
      $this->exists = TRUE;
      return $this;
    }
  
    if ($status == 201) {
      $path_parts = explode('/', $this->headers['location']);
      $this->key = array_pop($path_parts);
    }

    # Possibly json_decode...
    if (($status == 200 || $status == 201) && $this->jsonize) {
      $this->data = json_decode($this->data, true);
    }
    
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
   * Private.
   * @return $this
   */
  private function populateLinks($linkHeaders) {
    $linkHeaders = explode(",", trim($linkHeaders));
    foreach ($linkHeaders as $linkHeader) {
      $linkHeader = trim($linkHeader);
      $matches = array();
      $result = preg_match("/\<\/([^\/]+)\/([^\/]+)\/([^\/]+)\>; ?riaktag=\"([^\"]+)\"/", $linkHeader, $matches);
      if ($result == 1) {
        $this->links[] = new RiakLink(urldecode($matches[2]), urldecode($matches[3]), urldecode($matches[4]));
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
   * @return RiakObject.
   */
  function getSibling($i, $r=NULL) {
    # Use defaults if not specified.
    $r = $this->bucket->getR($r);

    # Run the request...
    $vtag = $this->siblings[$i];
    $params = array('r' => $r, 'vtag' => $vtag);
    $url = RiakUtils::buildRestPath($this->client, $this->bucket, $this->key, NULL, $params);
    $response = RiakUtils::httpRequest('GET', $url);
    
    # Respond with a new object...
    $obj = new RiakObject($this->client, $this->bucket, $this->key);
    $obj->jsonize = $this->jsonize;
    $obj->populate($response, array(200));
    return $obj;
  }

  /**
   * Retrieve an array of siblings.
   * @param integer $r - R-Value. Wait until this many partitions have
   * responded before returning to client.
   * @return array of RiakObject
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
   * @see RiakMapReduce::add()
   * @return RiakMapReduce
   */
  function add($params) {
    $mr = new RiakMapReduce($this->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "add"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::link()
   * @return RiakMapReduce
   */
  function link($params) {
    $mr = new RiakMapReduce($this->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "link"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::map()
   * @return RiakMapReduce
   */
  function map($params) {
    $mr = new RiakMapReduce($this->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "map"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see RiakMapReduce::reduce()
   * @return RiakMapReduce
   */
  function reduce($params) {
    $mr = new RiakMapReduce($this->client);
    $mr->add($this->bucket->name, $this->key);
    $args = func_get_args();
    return call_user_func_array(array(&$mr, "reduce"), $args);
  }
}

/**
 * Private class used to accumulate a CURL response.
 * @package RiakStringIO
 */
class RiakStringIO {
  function RiakStringIO() {
    $this->contents = '';
  }

  function write($ch, $data) {
    $this->contents .= $data;
    return strlen($data);
  }

  function contents() {
    return $this->contents;
  }
}

/**
 * Utility functions used by Riak library.
 * @package RiakUtils
 */
class RiakUtils {

  public static function get_value($key, $array, $defaultValue) {
    if (array_key_exists($key, $array)) {
      return $array[$key];
    } else {
      return $defaultValue;
    }
  }

  /**
   * Given a RiakClient, RiakBucket, Key, LinkSpec, and Params,
   * construct and return a URL.
   */
  public static function buildRestPath($client, $bucket=NULL, $key=NULL, $spec=NULL, $params=NULL) {
    # Build 'http://hostname:port/prefix/bucket'
    $path = 'http://';
    $path.= $client->host . ':' . $client->port;
    $path.= '/' . $client->prefix;
    
    # Add '.../bucket'
    if (!is_null($bucket) && $bucket instanceof RiakBucket) {
      $path .= '/' . urlencode($bucket->name);
    }
    
    # Add '.../key'
    if (!is_null($key)) {
      $path .= '/' . urlencode($key);
    }

    # Add '.../bucket,tag,acc/bucket,tag,acc'
    if (!is_null($spec)) {
      $s = '';
      foreach($spec as $el) {
	if ($s != '') $s .= '/';
	$s .= urlencode($el[0]) . ',' . urlencode($el[1]) . ',' . $el[2] . '/';
      }
      $path .= '/' . $s;
    }

    # Add query parameters.
    if (!is_null($params)) {
      $s = '';
      foreach ($params as $key => $value) {
	if ($s != '') $s .= '&';
	$s .= urlencode($key) . '=' . urlencode($value);
      }

      $path .= '?' . $s;
    }

    return $path;
  }

  /**
   * Given a RiakClient, RiakBucket, Key, LinkSpec, and Params,
   * construct and return a URL for searching secondary indexes.
   * @author Eric Stevens <estevens@taglabsinc.com>
   * @param RiakClient $client
   * @param RiakBucket $bucket
   * @param string $index - Index Name & type (eg, "indexName_bin")
   * @param string|int $start - Starting value or exact match if no ending value
   * @param string|int $end - Ending value for range search
   * @param array $params - Any extra query parameters to pass on the URL
   * @return string URL
   */
  public static function buildIndexPath(RiakClient $client, RiakBucket $bucket, $index, $start, $end=NULL, array $params=NULL) {
    # Build 'http://hostname:port/prefix/bucket'
    $path = array('http:/',$client->host.':'.$client->port,$client->indexPrefix);

    # Add '.../bucket'
    $path[] = urlencode($bucket->name);
    
    # Add '.../index'
    $path[] = 'index';
    
    # Add '.../index_type'
    $path[] = urlencode($index);
    
    # Add .../(start|exact)
    $path[] = urlencode($start);
    
    if (!is_null($end)) {
      $path[] = urlencode($end);
    }
    
    // faster than repeated string concatenations
    $path = join('/', $path);

    # Add query parameters.
    if (!is_null($params)) {
      // Most users will have the http PECL extension
      if (func_exists('http_build_query')) {
        $path .= '?' . http_build_query($params, '', '&');
      } else {
        // In case they don't have the http PECL extension
        $s = array();
        foreach ($params as $key => $value) {
          $s[] = urlencode($key) . '=' . urlencode($value);
        }

        $path .= '?' . join('&', $s);
      }
    }

    return $path;
  }

  /**
   * Given a Method, URL, Headers, and Body, perform and HTTP request,
   * and return an array of arity 2 containing an associative array of
   * response headers and the response body.
   */
  public static function httpRequest($method, $url, $request_headers = array(), $obj = '') {
    # Set up curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

    if ($method == 'GET') {
      curl_setopt($ch, CURLOPT_HTTPGET, 1);
    } else if ($method == 'POST') {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
    } else if ($method == 'PUT') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
    } else if ($method == 'DELETE') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    # Capture the response headers...
    $response_headers_io = new RiakStringIO();
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$response_headers_io, 'write'));

    # Capture the response body...
    $response_body_io = new RiakStringIO();
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, array(&$response_body_io, 'write'));

    try {
      # Run the request.
      curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      # Get the headers...
      $parsed_headers = RiakUtils::parseHttpHeaders($response_headers_io->contents());
      $response_headers = array("http_code"=>$http_code);
      foreach ($parsed_headers as $key=>$value) {
        $response_headers[strtolower($key)] = $value;
      }
      
      # Get the body...
      $response_body = $response_body_io->contents();

      # Return a new RiakResponse object.
      return array($response_headers, $response_body);
    } catch (Exception $e) {
      curl_close($ch);
      error_log('Error: ' . $e->getMessage());
      return NULL;
    } 
  }

  /**
   * Parse an HTTP Header string into an asssociative array of
   * response headers.
   */
  static function parseHttpHeaders($headers) {
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach( $fields as $field ) {
      if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
        $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
        if( isset($retVal[$match[1]]) ) {
          $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }
}



<?php
namespace Riak;

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
 * @package Riak
 */
class Client {

    protected $transport;
    protected $r;
    protected $w;
    protected $dw;


  /**
   * Construct a new Riak\Client object.
   *
   * OLD WAY - backward compatible
   * @param string $host - Hostname or IP address (default '127.0.0.1')
   * @param int $port - Port number (default 8098)
   * @param string $prefix - Interface prefix (default "riak")
   * @param string $mapred_prefix - MapReduce prefix (default "mapred")
   *
   * NEW WAY - preferred
   * @param Transport\Iface $transport
   */
  function __construct( $transport = NULL) {
    if( ! $transport instanceof Transport\Iface ){
        $args = func_get_args();
        $host = isset( $args[0] ) ? $args[0] : '127.0.0.1';
        $port = isset( $args[1] ) ? $args[1] : 8098;
        if( $port == 8087 ){
            $client_id = isset( $args[2] ) ? $args[2] : NULL;
            $transport = new Transport\ProtoBuf( $host, $port, $client_id );
        } else {
            $prefix = isset( $args[2] ) ? $args[2] : 'riak';
            $mapred_prefix = isset( $args[3] ) ? $args[3] : 'mapred';
            $transport = new Transport\Http( $host, $port, $prefix, $mapred_prefix );
        }
    }
    $this->transport = $transport;
    $this->r = 2;
    $this->w = 2;
    $this->dw = 2;
  }

  /**
   * Get the R-value setting for this Riak\Client. (default 2)
   * @return integer
   */
  function getR() { 
    return $this->r; 
  }

  /**
   * Set the R-value for this Riak\Client. This value will be used
   * for any calls to get(...) or getBinary(...) where where 1) no
   * R-value is specified in the method call and 2) no R-value has
   * been set in the Riak\Bucket.  
   * @param integer $r - The R value.
   * @return $this
   */
  function setR($r) { 
    $this->r = $r; 
    return $this; 
  }

  /**
   * Get the W-value setting for this Riak\Client. (default 2)
   * @return integer
   */
  function getW() { 
    return $this->w; 
  }

  /**
   * Set the W-value for this Riak\Client. See setR(...) for a
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
   * Set the DW-value for this Riak\Client. See setR(...) for a
   * description of how these values are used.
   * @param  integer $dw - The DW value.
   * @return $this
   */
  function setDW($dw) { 
    $this->dw = $dw; 
    return $this; 
  }

  /**
   * Get the clientID for this Riak\Client.
   * @return string
   */
  function getClientID() { 
    return $this->transport->getClientId(); 
  }

  /**
   * Set the clientID for this Riak\Client. Should not be called
   * unless you know what you are doing.
   * @param string $clientID - The new clientID.
   * @return $this
   */
  function setClientID($clientid) { 
    $this->transport->setClientId( $clientid ); 
    return $this;
  }

  /**
   * Get the bucket by the specified name. Since buckets always exist,
   * this will always return a Riak\Bucket.
   * @return Riak\Bucket
   */
  function bucket($name) {
    return new Bucket($this, $name);
  }

  /**
   * Get all buckets.
   * @return array() of Riak\Bucket objects
   */
  function buckets() {
    foreach($this->transport->getBuckets() as $name) {
        $buckets[] = $this->bucket($name);
    }
    return $buckets;
  }

  /**
   * Check if the Riak server for this Riak\Client is alive.
   * @return boolean
   */
  function isAlive() {
    return $this->transport->ping();
  }


  # MAP/REDUCE/LINK FUNCTIONS

  /**
   * Start assembling a Map/Reduce operation.
   * @see Riak\MapReduce::add()
   * @return Riak\MapReduce
   */
  function add($params) {
    $mr = new MapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array($mr, "add"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation. This command will 
   * return an error unless executed against a Riak Search cluster.
   * @see Riak\MapReduce::search()
   * @return Riak\MapReduce
   */
  function search($params) {
    $mr = new MapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array($mr, "search"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see MapReduce::link()
   */
  function link($params) {
    $mr = new MapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array($mr, "link"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see MapReduce::map()
   */
  function map($params) {
    $mr = new MapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array($mr, "map"), $args);
  }

  /**
   * Start assembling a Map/Reduce operation.
   * @see MapReduce::reduce()
   */
  function reduce($params) {
    $mr = new MapReduce($this);
    $args = func_get_args();
    return call_user_func_array(array($mr, "reduce"), $args);
  }
  
  /**
  * access protected properties of the object ... just can't set them.
  */
  public function __get( $k ){
    return $this->$k;
  }
}


<?php
namespace Riak\Transport;

/*
* Class to encapsulate transport details
*/
interface Iface {
    
   /**
    * Ping the remote server
    *    @return boolean
    */
    public function ping();

   /**
    * Serialize get request and deserialize response
    * @return (vclock=null, [(metadata, value)]=null)
    */   
    public function get($object, $r = null, $vtag = null);
       
   /**
    * Serialize put request and deserialize response - if 'content'
    * is true, retrieve the updated metadata/content
    * @return (vclock=null, [(metadata, value)]=null)
    */
    public function put($object, $w = null, $dw = null);

   /**
    * Serialize delete request and deserialize response
    * @return true
    */
    public function delete($object, $rw = null);

   /**
    * Serialize get buckets request and deserialize response
    * @return list of keys
    */
    public function getBuckets();
    
    /**
    * Serialize get bucket property request and deserialize response
    * @return hash table of properties
    */
    public function getBucketProps($bucket);

   /**
    * Serialize set bucket property request and deserialize response
    * bucket = bucket object
    * props = assoc array of properties
    * @return boolean
    */
    public function setBucketProps($bucket, $props);
    
    public function getBucketKeys( $bucket, $cb = NULL );

   /**
    * Serialize map/reduce request
    */
    public function mapred($inputs, $query, $timeout = null);
    
    public function indexSearch($bucket, $indexName, $indexType, $startOrExact = null, $end=NULL, $dedupe=false);


   /**
    * Set the client id. This overrides the default, random client id, which is automatically
    * generated when none is specified in when creating the transport object.
    */
    public function setClientId($client_id);

   /**
    * Fetch the client id for the transport.
    */
    public function getClientId();

}
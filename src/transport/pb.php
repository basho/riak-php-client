<?php
namespace Riak\Transport;
use Riak\Exception;
use Riak\Link;
use Riak\PB as Message;
/*
* Http transport class
*/
class PB implements Iface {

    public $host;
    public $port;
    protected $stream;
    

    function __construct($host='127.0.0.1', $port=8087, $client_id = NULL ) {
        $this->host = $host;
        $this->port = $port;
        if( $client_id === TRUE ){
            $client_id = 'php_' . base_convert(mt_rand(), 10, 36);
        }
        if( $client_id !== NULL ) $this->setClientId( $client_id );
    }
    
    public function connect(){
        if( $this->stream ) return $this->stream;
        $fp = fsockopen( $this->host, $this->port );
        if( ! $fp ) throw new Exception("unable to connect to riak protocol buffer interface");
        return $this->stream = $fp;
    }
    
    public function close (){
        if( ! $this->stream ) return;
        fclose( $this->stream );
        unset( $this->stream );
    }
    
    public function __destruct(){
        $this->close();
    }
    
    public function stream(){
        return new Message\Stream( $this->connect() );
    }
    
   /**
    * Ping the remote server
    *    @return boolean
    */
    public function ping(){
        try {
            $res = $this->stream()->send(new Message\PingReq());
            if( ! $res instanceof Message\PingResp ) return FALSE;            
            return TRUE;
        } catch ( Exception $e ){
            return FALSE;
        }
    }

   /**
    * Serialize get request and deserialize response
    * @return (vclock=null, [(metadata, value)]=null)
    */   
    public function get($object, $r = null, $vtag = null){
        $req = new Message\GetReq();
        $req->bucket = $object->bucket->getName();
        $req->key = $key = $object->getKey();
        $req->r = $r;
        $req->notfound_ok = true;
        $res = $this->stream()->send($req);
        if( ! $res instanceof Message\GetResp ){
            throw new Exception('unable to get object: ' . $key);
        }        
        return $this->parseObjectResponse( $res, $vtag );
    }
       
   /**
    * Serialize put request and deserialize response - if 'content'
    * is true, retrieve the updated metadata/content
    * @return (vclock=null, [(metadata, value)]=null)
    */
    public function put($object, $w = null, $dw = null){
        $content = new Message\Content();
        $content->value =  $object->jsonize ? json_encode($object->getData()) : $object->getData();
        $content->content_type = $object->getContentType();
        foreach( $object->links as $link ){
            $pblink = $content->add('links', new Message\Link());
            $pblink->bucket = $link->getBucket();
            $pblink->key = $link->getKey();
            $pblink->tag = $link->getTag();
        }

        # Add the auto indexes...
        $collisions = array();
        $indexes = $object->indexes;
        foreach($object->autoIndexes as $index=>$fieldName) {
          $value = null;
          // look up the value
          if (isset($object->data[$fieldName])) {
            $value = $object->data[$fieldName];
            $pbpair = new Message\Pair;
            $pbpair->key = $index;
            $pbpair->value = $value;
            $content->add('indexes', $pbpair);
            
            // look for value collisions with normal indexes
            if (isset($object->indexes[$index])) {
              if (false !== array_search($value, $object->indexes[$index])) {
                $collisions[$index] = $value;
              }
            }
          }
        }
        
        $object->setMeta('x-rc-autoindex', 
            count($object->autoIndexes) > 0 ? json_encode($object->autoIndexes) : NULL);
        
        $object->setMeta('x-rc-autoindexcollision', 
            count($collisions) > 0 ? json_encode($collisions) : null );
        
        
        foreach( $object->meta as $k=>$v ){
            if( $v === NULL ) continue;
            $pbpair = $content->add('usermeta', new Message\Pair);
            $pbpair->key = $k;
            $pbpair->value = $v;
        }
        
        foreach( $object->indexes as $k=>$values ){
            foreach( $values as $v ){
                $pbpair = $content->add('indexes',  new Message\Pair);
                $pbpair->key = $k;
                $pbpair->value = $v;
            }
        }
        
        $req = new Message\PutReq();
        $req->bucket = $object->bucket->getName();
        if( $key = $object->getKey() ) $req->key = $key;
        if( $object->vclock ) $req->vclock = $object->vclock;
        $req->content = $content;
        $req->return_body = true;
        $req->w = $w;
        $req->dw = $dw;
        $res = $this->stream()->send($req);
        if( ! $res instanceof Message\PutResp ){
            throw new Exception('unable to set object: ' . $key, $res);
        }        
        return $this->parseObjectResponse($res);
    
    }

   /**
    * Serialize delete request and deserialize response
    * @return true
    */
    public function delete($object, $dw = null){
        $req = new Message\DelReq();
        $req->bucket = $object->bucket->getName();
        $req->key = $key = $object->getKey();
        $req->dw = $dw;
        $res = $this->stream()->send($req);
        
        if( ! $res instanceof Message\DelResp ){
            throw new Exception('unable to delete object: ' . $key, $req);
        }
        return $this->responseObject();
    }

   /**
    * Serialize get buckets request and deserialize response
    * @return list of keys
    */
    public function getBuckets(){
        $res = $this->stream()->send(new Message\ListBucketsReq());
        if( ! $res instanceof Message\ListBucketsResp ){
            throw new Exception('unable to list buckets');
        }
        return $res->buckets;
    }
    
    /**
    * Serialize get bucket property request and deserialize response
    * @return hash table of properties
    */
    public function getBucketProps($bucket){
        $req = new Message\GetBucketReq();
        $req->bucket = $bucket;
        $res = $this->stream()->send($req);
        if( ! $res instanceof Message\GetBucketResp ){
            throw new Exception('unable to get bucket properties');
        }
        $props = array();
        $p = $res->props;
        foreach( array('n_val', 'allow_mult') as $k){
            $props[ $k ] = $p->$k;
        }
        $result = $this->responseObject();
        $result->data = array('props'=>$props );
        return $result;
    }

   /**
    * Serialize set bucket property request and deserialize response
    * bucket = bucket object
    * props = assoc array of properties
    * @return boolean
    */
    public function setBucketProps($bucket, $props){
        $p = new Message\BucketProps();
        foreach( $props as $k => $v ){
            $p->$k = $v;
        }
         foreach( $props as $k => $v ){
            if( $k == 'n_val') $p->n_val = $v;
            if( $k == 'allow_mult') $p->allow_mult = $v;
        }
        $req = new Message\SetBucketReq();
        $req->bucket = $bucket;
        $req->props = $p;
        $res = $this->stream()->send($req);
        if( ! $res instanceof Message\SetBucketResp ){
            throw new Exception('unable to set bucket properties');
        }
    }
    
    public function getBucketKeys( $bucket, $cb = NULL ){
        $req = new Message\ListKeysReq();
        $req->bucket = $bucket;
        $keys = array();
        $stream = $this->stream();
        $stream->write($req);
        do {
            $res = $stream->read();
            if( ! $res instanceof Message\ListKeysResp ){
                throw new Exception('unable to get bucket keys');
            }
            foreach( $res->keys as $key ) $keys[] = $key;
        } while( ! $res->done );
        $result = $this->responseObject();
        $result->data = array('keys'=>$keys );
        return $result;
    }
    
    public function indexSearch($bucket, $indexName, $indexType, $startOrExact = null, $end=NULL, $dedupe=false) {
       
        $inputs = array('bucket'=>$bucket, 'index'=>$indexName . '_' . $indexType );
        if( $end === NULL ){
            $inputs['key'] = $startOrExact;
        } else {
            $inputs['start'] = $startOrExact;
            $inputs['end'] = $end;
        }
        
        $query = array( array(
            'reduce'=>array(
                'language'=>'erlang', 
                'module'=>'riak_kv_mapreduce',
                'function'=>'reduce_identity',
                ),
        ));
        
        $result = $this->mapred( $inputs, $query );
        $obj = $this->responseObject();
        $keys = array();
        foreach( $result as $info ) $keys[] = $info[1];
        $obj->data = array( 'keys'=>$keys );
        return $obj;
    }

   /**
    * Serialize map/reduce request
    */
    public function mapred($inputs, $query, $timeout = null){
        # Construct the job, optionally set the timeout...
        $job = array("inputs"=>$inputs, "query"=>$query);
        if ($timeout != NULL) $job["timeout"] = $timeout;
        
        $req = new Message\MapRedReq();
        $req->content_type = 'application/json';
        $req->request = json_encode($job);        
        $stream = $this->stream();
        $stream->write($req);
        $result = array();
        do {
            $res = $stream->read();
            if( ! $res instanceof Message\MapRedResp ){
                throw new Exception('unable to run map reduce job', $res);
            }
            $r = json_decode( $res->response );
            if( is_array( $r ) ){
                foreach( $r as $_r ) $result[] = $_r;
            }
        } while( ! $res->done );
        return $result;
    }

   /**
    * Set the client id. This overrides the default, random client id, which is automatically
    * generated when none is specified in when creating the transport object.
    */
    public function setClientId($client_id){
        $req = new Message\SetClientIdReq();
        $req->client_id = $client_id;
        $res = $this->stream()->send($req);
        if( ! $res instanceof Message\SetClientIdResp ){
            throw new Exception('unable to set client id');
        }
        return true;
    }

   /**
    * Fetch the client id for the transport.
    */
    public function getClientId(){
        $res = $this->stream()->send(new Message\GetClientIdReq());
        if( ! $res instanceof Message\GetClientIdResp ){
            throw new Exception('unable to get client id');
        }
        return $res->client_id;
    }
    
  function parseObjectResponse($res, $vtag = NULL ) {
        $siblings  = $res->content;
        if( $vtag === NULL ){
            $content = $siblings ? $siblings[0] : NULL;
        } else {
            $content = NULL;
            foreach( $siblings as $sibling ){
                if( $sibling->vtag == $vtag ){
                    $content = $sibling;
                    break;
                }
            }
        }
        if( ! $content ) return NULL;
        
        $result = $this->responseObject();
        
        $result->data = $content->value;
        $result->content_type = $content->content_type;
        $result->vclock = $res->vclock;
        foreach( $content->links as $link ){
            $result->links[] = new Link( $link->bucket, $link->key, $link->tag );
        }
        
        foreach( $content->usermeta as $pair ){
            $result->meta[ $pair->key ] = $pair->value;
        }
        
        foreach( $content->indexes as $pair ){
            $k = $pair->key;
            $v = $pair->value;
            if( ! isset( $result->indexes[ $k ] ) ) $result->indexes[ $k ] = array();
            $result->indexes[ $k ][] = $v;
        }
        
        if( $res instanceof Message\PutResp ){
            if( ( $key = $res->key ) !== NULL ) $result->key = $key;
        }
        
        if( count( $siblings ) > 1 ){
            foreach( $siblings as $content ){
                $result->siblings[] = $content->vtag;
            }
        }

        return $result;
  }
  
  protected function responseObject(){
        $result = new \stdclass;
        $result->vclock = NULL;
        $result->content_type = NULL;
        $result->key = NULL;
        $result->links = array();
        $result->meta = array();
        $result->indexes = array();
        $result->data = NULL;
        $result->siblings = NULL;
        return $result;
  }
}
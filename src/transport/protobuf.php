<?php
namespace Riak\Transport;
use Riak\Exception;
use Riak\Link;
use Riak\ProtoBuf as PB;
/*
* Http transport class
*/
class ProtoBuf implements Iface {

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
        return new PB\Stream( $this->connect() );
    }
    
   /**
    * Ping the remote server
    *    @return boolean
    */
    public function ping(){
        try {
            $res = $this->stream()->send(new PB\PingReq());
            if( ! $res instanceof PB\PingResp ) return FALSE;            
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
        $req = new PB\GetReq();
        $req->setBucket( $object->bucket->getName() );
        $req->setKey( $key = $object->getKey() );
        $req->setR( $r );
        $req->setNotfoundOk(true);
        $res = $this->stream()->send($req);
        if( ! $res instanceof PB\GetResp ){
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
        $content = new PB\Content();
        $content->setValue(  $object->jsonize ? json_encode($object->getData()) : $object->getData() );
        $content->setContentType( $object->getContentType() );
        $content->setVTag( $object->vclock );
        
        foreach( $object->links as $link ){
            $pblink = new PB\Link();
            $pblink->setBucket($link->getBucket());
            $pblink->setKey($link->getKey());
            $pblink->setTag($link->getTag());
            $content->addLinks( $pblink );
        }

        # Add the auto indexes...
        $collisions = array();
        $indexes = $object->indexes;
        foreach($object->autoIndexes as $index=>$fieldName) {
          $value = null;
          // look up the value
          if (isset($object->data[$fieldName])) {
            $value = $object->data[$fieldName];
            $pbpair = new PB\Pair;
            $pbpair->setKey( $index );
            $pbpair->setValue( $value );
            $content->addIndexes( $pbpair );
            
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
            $pbpair = new PB\Pair;
            $pbpair->setKey( $k );
            $pbpair->setValue( $v );
            $content->addUserMeta( $pbpair );
        }
        
        foreach( $object->indexes as $k=>$values ){
            foreach( $values as $v ){
                $pbpair = new PB\Pair;
                $pbpair->setKey( $k );
                $pbpair->setValue( $v );
                $content->addIndexes( $pbpair );
            }
        }
        
        $req = new PB\PutReq();
        $req->setBucket( $object->bucket->getName() );
        $req->setKey( $key = $object->getKey() );
        $req->setVclock( $object->vclock );
        $req->setContent( $content );
        $req->setReturnBody( true );
        $req->setW( $w );
        $req->setDW( $dw );
        $res = $this->stream()->send($req);
        if( ! $res instanceof PB\PutResp ){
            throw new Exception('unable to set object: ' . $key, $res);
        }        
        return $this->parseObjectResponse($res);
    
    }

   /**
    * Serialize delete request and deserialize response
    * @return true
    */
    public function delete($object, $dw = null){
        $req = new PB\DelReq();
        $req->setBucket( $object->bucket->getName() );
        $req->setKey( $key = $object->getKey() );
        //$req->setRW( ???? );
        $req->setDW( $dw );
        //$req->setPR( ????);
        //$req->setPW( ???? );
        
         $res = $this->stream()->send($req);
        
        if( ! $res instanceof PB\DelResp ){
            throw new Exception('unable to delete object: ' . $key);
        }
        return $this->responseObject();
    }

   /**
    * Serialize get buckets request and deserialize response
    * @return list of keys
    */
    public function getBuckets(){
        $res = $this->stream()->send(new PB\ListBucketsReq());
        if( ! $res instanceof PB\ListBucketsResp ){
            throw new Exception('unable to list buckets');
        }
        return $res->getBucketsArray();
    }
    
    /**
    * Serialize get bucket property request and deserialize response
    * @return hash table of properties
    */
    public function getBucketProps($bucket){
        $req = new PB\GetBucketReq();
        $req->setBucket( $bucket );
        $res = $this->stream()->send($req);
        if( ! $res instanceof PB\GetBucketResp ){
            throw new Exception('unable to get bucket properties');
        }
        $props = array();
        $p = $res->getProps();
        foreach( array('n_val'=>'getNVal', 'allow_mult'=>'getAllowMult') as $k => $v ){
            $props[ $k ] = $p->$v();
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
        $p = new PB\BucketProps();
        foreach( $props as $k => $v ){
            if( $k == 'n_val') $p->setNVal( $v );
            if( $k == 'allow_mult') $p->setAllowMult( $v );
        }
        $req = new PB\SetBucketReq();
        $req->setBucket( $bucket );
        $req->setProps( $p );
        $res = $this->stream()->send($req);
        if( ! $res instanceof PB\SetBucketResp ){
            throw new Exception('unable to set bucket properties');
        }
    }
    
    public function getBucketKeys( $bucket, $cb = NULL ){
        $req = new PB\ListKeysReq();
        $req->setBucket( $bucket );
        $keys = array();
        $stream = $this->stream();
        $stream->write($req);
        do {
            $res = $stream->read();
            if( ! $res instanceof PB\ListKeysResp ){
                throw new Exception('unable to get bucket keys');
            }
            foreach( $res->getKeysArray() as $key ) $keys[] = $key;
        } while( ! $res->getDone() );
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
        
        $req = new PB\MapRedReq();
        $req->setContentType('application/json');
        $req->setRequest( json_encode($job) );        
        $stream = $this->stream();
        $stream->write($req);
        $result = array();
        do {
            $res = $stream->read();
            if( ! $res instanceof PB\MapRedResp ){
                throw new Exception('unable to run map reduce job', $res);
            }
            $r = json_decode( $res->GetResponse() );
            if( is_array( $r ) ){
                foreach( $r as $_r ) $result[] = $_r;
            }
        } while( ! $res->getDone() );
        return $result;
    }

   /**
    * Set the client id. This overrides the default, random client id, which is automatically
    * generated when none is specified in when creating the transport object.
    */
    public function setClientId($client_id){
        $req = new PB\SetClientIdReq();
        $req->setClientId( $client_id );
        $res = $this->stream()->send($req);
        if( ! $res instanceof PB\SetClientIdResp ){
            throw new Exception('unable to set client id');
        }
        return true;
    }

   /**
    * Fetch the client id for the transport.
    */
    public function getClientId(){
        $res = $this->stream()->send(new PB\GetClientIdReq());
        if( ! $res instanceof PB\GetClientIdResp ){
            throw new Exception('unable to get client id');
        }
        return $res->getClientId();
    }
    
  function parseObjectResponse($res, $vtag = NULL ) {
        $siblings  = $res->getContentArray();
        
        if( $vtag === NULL ){
            $content = $siblings ? $siblings[0] : NULL;
        } else {
            $content = NULL;
            foreach( $siblings as $sibling ){
                if( $sibling->getVtag() == $vtag ){
                    $content = $sibling;
                    break;
                }
            }
        }
        if( ! $content ) return NULL;
        
        $result = $this->responseObject();
        
        $result->data = $content->getValue();
        $result->content_type = $content->getContentType();
        $result->vclock = $res->getVClock();
        foreach( $content->getLinksArray() as $link ){
            $result->links[] = new Link( $link->getBucket(), $link->getKey(), $link->getTag() );
        }
        
        foreach( $content->getUserMetaArray() as $pair ){
            $result->meta[ $pair->getKey() ] = $pair->getValue();
        }
        
        foreach( $content->getIndexesArray() as $pair ){
            $k = $pair->getKey();
            $v = $pair->getValue();
            if( ! isset( $result->indexes[ $k ] ) ) $result->indexes[ $k ] = array();
            $result->indexes[ $k ][] = $v;
        }
        
        
        if( $res instanceof PB\PutResp ){
            if( ( $key = $res->getKey() ) !== NULL ) $result->key = $key;
        }
        
        if( count( $siblings ) > 1 ){
            foreach( $siblings as $content ){
                $result->siblings[] = $content->getVTag();
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
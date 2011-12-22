<?php
namespace Riak\Transport;

/*
* Use this class to run curl calls easily.
* $request = new Request('http://news.yahoo.com/rss/');
* $request->post = array('param1'=>1, 'param2'=>2);
* $response = $request->send();
* print PHP_EOL . "URL: " . $request->url;
* print PHP_EOL . "RESPONSE: " $response->body;
*/
class HttpRequest extends \StdClass {

    public $url;
    public $post;
    public $method;
    public $headers = array();
    public $resource;
    public $build;
    public $handle;
    public $response;



   /**
    * import data from a different http class into this one.
    * @param mixed      either an array or http object
    * @return void
    */
    public function __construct( $data ){
        if(is_resource( $data ) && get_resource_type($data) == 'curl'){
            $this->resource = $data;
            if( ! isset( $this->url) ) $this->url = curl_getinfo($this->resource, CURLINFO_EFFECTIVE_URL);
        } elseif( is_string( $data ) ){
            $this->url = $data;
        } elseif( is_array( $data ) || $data instanceof Iterator ) {
            foreach( $data as $k=>$v ) $this->$k = $v;
        }
    }
    
   /**
    * try to run the request right away.
    * @return Data of the response.
    * Usually this is the only method you need to know if you are using this object on its own.
    * Allows you to run a curl call and get response back.
    * If you need to do multiple calls in parallel, look at the pool class.
    */
    public function send( array $opts = array() ){
        $this->build( $opts );
        curl_exec( $this->resource );
        return $this->handle();
    }
    
    public function exec( array $opts = array() ){
        return $this->send( $opts );
    }
        
   /**
    * utility method. send the Http request out through a stream and return the stream object
    * @param array    curl opts.
    */
    public function build( array $opts = array() ){
        if( isset( $this->resource ) && get_resource_type($this->resource) == 'curl' ){
            $ch = $this->resource;
            if( ! $this->url ) $this->url = curl_getinfo($this->resource, CURLINFO_EFFECTIVE_URL);
            curl_setopt($this->resource, CURLOPT_HTTPGET, 1);
            curl_setopt( $this->resource, CURLOPT_HEADER, FALSE);
        } else {
            $ch = $this->resource = curl_init();
        }
                
        if( ! isset($opts[CURLOPT_HTTPHEADER]) )$opts[CURLOPT_HTTPHEADER] = array();
        
        foreach( $this->headers as $k => $v ){
            if( is_int( $k ) ){
                $opts[CURLOPT_HTTPHEADER][] = $v;
            } else {
                $opts[CURLOPT_HTTPHEADER][] = $k . ': ' . $v;
            }
        }   
        
        $opts[ CURLOPT_URL ] = $this->url;
        if( isset( $this->post ) ) {
            $opts[CURLOPT_POST] = 1;
            $post = $this->post;
            $opts[CURLOPT_POSTFIELDS] =  is_array( $this->post ) ? self::buildQuery( $this->post ) : $this->post;
        } 
        
        if ( isset( $this->method ) ){
            if( isset( $opts[CURLOPT_POST]) ) unset( $opts[CURLOPT_POST] );
            $opts[CURLOPT_CUSTOMREQUEST] = strtoupper( $this->method );
        }
        $opts[CURLINFO_HEADER_OUT] = 1;
        $this->response = $r = (object) array('request_header'=>'', 'response_header'=>'', 'body'=> '', 'http_code'=>0);
        
        if( ! isset( $opts[CURLOPT_WRITEFUNCTION] ) ) {
            $opts[CURLOPT_WRITEFUNCTION ] = function ( $ch, $data ) use( $r ) {
                $r->body .= $data;
                return strlen( $data );
            };
        }
        
        if( ! isset( $opts[CURLOPT_HEADERFUNCTION] ) ) {
            $opts[CURLOPT_HEADERFUNCTION] = function ( $ch, $data ) use( $r ) {
                $r->response_header .= $data;
                return strlen( $data );
            };
        }
        
        if( isset( $this->build ) && $this->build instanceof \Closure ){
            $cb = $this->build;
            $cb( $this, $opts );
        }
        curl_setopt_array( $ch, $opts );

        return $ch;
    }
    
   /**
    * Handle the response ... internal method only. Used by the Pool class.
    */
    public function handle(){  
        $response = $this->response;
        if( $info = $this->getInfo() ){
            foreach( $info as $k => $v ) $response->$k = $v;
        }
        if( isset( $this->handle) && $this->handle instanceof \Closure ) {
            $cb = $this->handle;
            $cb( $this->response );
        }
        return $this->response;
    }
    
    /**
    * get info about the resource.
    * returns false if no resource.
    */
    public function getinfo(){
         if( ! $this->resource ) return FALSE;
         if( get_resource_type($this->resource) != 'curl' ) return false;
         return curl_getinfo( $this->resource );
    }
    
    /*
    * close the curl handle.
    */
    public function close(){
        if( $this->resource && get_resource_type($this->resource) == 'curl' ) curl_close( $this->resource );
        unset( $this->resource );
    }
    
    public function __destruct( ){
        $this->close();
    }
    
    public static function buildQuery($params, $name=null) {
        if( is_object( $params ) ) $params = json_decode( json_encode( $params ), TRUE);
        if( ! is_array( $params ) ) return rawurlencode($params);
        $ret = "";
        foreach($params as $key=>$val) {
            $key = rawurlencode( $key );
            if(is_array($val)) {
                if($name==null) $ret .= self::buildQuery($val, $key);
                else $ret .= self::buildQuery($val, $name."[$key]");   
            } else {
                $val=rawurlencode($val);
                if($name!=null)
                $ret.=$name."[$key]"."=$val&";
                else $ret.= "$key=$val&";
            }
        }
        if( $name == null ) $ret = trim( $ret, '&');
        return $ret;   
    }
}
// EOC
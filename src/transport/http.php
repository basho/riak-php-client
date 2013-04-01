<?php
namespace Riak\Transport;
use Riak\Exception;
use Riak\Link;

/*
* Http transport class
*/
class Http implements Iface {

    public $host;
    public $port;
    public $prefix;
    public $mapred_prefix;
    public $indexPrefix;
    public $clientid;

    function __construct($host='127.0.0.1', $port=8098, $prefix='riak', $mapred_prefix='mapred') {
        $this->host = $host;
        $this->port = $port;
        $this->prefix = $prefix;    
        $this->mapred_prefix = $mapred_prefix;
        $this->indexPrefix='buckets';
        $this->clientid = 'php_' . base_convert(mt_rand(), 10, 36);
    }
    
   /**
    * Ping the remote server
    *    @return boolean
    */
    public function ping(){
        $response = $this->http( '/ping', array(200) )->send();
        return ($response->body == 'OK');
    }

   /**
    * Serialize get request and deserialize response
    * @return (vclock=null, [(metadata, value)]=null)
    */
    public function get($object, $r = null, $vtag = null){
        return $this->prepare_get( $object, $r, $vtag )->send()->result;
    }
    
    public function prepare_get($object, $r = null, $vtag = null){
        $params = array();
        if( $r != NULL ) $params['r'] = $r;
        if( $vtag != NULL ) $params['vtag'] = $vtag;
        $uri = $this->buildRestPath($object->bucket->getName(), $object->key, NULL, $params);
        $http = $this->http( $uri, array(200, 300, 404));

        # prepare the http request 
        $handle = $http->handle;
        $transport = $this;
        $http->handle = function( $response ) use ( $handle, $transport ){
            $handle( $response );
            $transport->parseObjectResponse( $response );
        };
        return $http;
    }
       
   /**
    * Serialize put request and deserialize response - if 'content'
    * is true, retrieve the updated metadata/content
    * @return (vclock=null, [(metadata, value)]=null)
    */
    public function put($object, $w = null, $dw = null){
        return $this->prepare_put( $object, $w, $dw )->send()->result;
    }
    
    public function prepare_put( $object, $w = null, $dw = NULL ){
        
        # Construct the headers...
        $headers = array('Accept: text/plain, */*; q=0.5',
                         'Content-Type: ' . $object->getContentType(),
                         'X-Riak-ClientId: ' . $object->bucket->client->getClientID());
    
        # Add the vclock if it exists...
        if ( ($vclock = $object->vclock ) != NULL) {
            $headers[] = 'X-Riak-Vclock: ' . $vclock;
        }
    
        # Add the Links...
        foreach ($object->links as $link) {
          $headers[] = 'Link: ' . $this->toLinkHeader($link);
        }
        
        # Add the auto indexes...
        $collisions = array();
        foreach($object->autoIndexes as $index=>$fieldName) {
          $value = null;
          // look up the value
          if (isset($object->data[$fieldName])) {
            $value = $object->data[$fieldName];
            $headers[] = "x-riak-index-$index: ".urlencode($value);
            
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
        
            
        # Add the indexes
        foreach ($object->indexes as $index=>$values) {
          $headers[] = "x-riak-index-$index: " . join(', ', array_map('urlencode', $values));
        }
        
        
        # Add the metadata...
        foreach($object->meta as $metaName=>$metaValue) {
          if ($metaValue !== null) $headers[] = "X-Riak-Meta-$metaName: $metaValue";
        }
        
        # Construct the URL...
        $params = array('returnbody' => 'true', 'w' => $w, 'dw' => $dw);
        $uri = $this->buildRestPath($object->bucket->getName(), $object->key, NULL, $params);
        
        # create the request
        $http = $this->http( $uri, array(200, 201, 300) );
        $http->headers = $headers;
        $http->method = $object->key ? 'PUT' : 'POST';
        $http->post = $object->jsonize ? json_encode($object->getData()) : $object->getData();
        
        # prepare the http request 
        $handle = $http->handle;
        $transport = $this;
        $http->handle = function( $response ) use ( $handle, $transport ){
            $handle( $response );
            $transport->parseObjectResponse( $response );
        };
        
        return $http;
    
    }

   /**
    * Serialize delete request and deserialize response
    * @return true
    */
    public function delete($object, $dw = null){
        return $this->prepare_delete( $object, $dw )->send()->result;
    }
    
    public function prepare_delete($object, $dw = null){
        # Construct the URL...
        $params = array('dw' => $dw);
        $uri = $this->buildRestPath($object->bucket->getName(), $object->key, NULL, $params);

        $http = $this->http( $uri, array(204, 404));
        $http->method = 'DELETE';

        # prepare the http request 
        $handle = $http->handle;
        $transport = $this;
        $http->handle = function( $response ) use ( $handle, $transport ){
            $handle( $response );
            $transport->parseObjectResponse( $response );
        };
        
        return $http;
    }

   /**
    * Serialize get buckets request and deserialize response
    * @return list of keys
    */
    public function getBuckets(){
        $uri = $this->buildRestPath() .'?buckets=true';
        $request = $this->http( $uri, array(200) );
        $response = $request->send();
        $data = json_decode($response->body, true);
        if( ! is_array( $data ) || ! isset( $data['buckets'] ) ) throw new Exception('invalid response', $response);
        return $data['buckets'];
    }
    
    /**
    * Serialize get bucket property request and deserialize response
    * @return hash table of properties
    */
    public function getBucketProps($bucket){
        return $this->prepare_getBucketProps( $bucket )->send()->result;
    }
    
    public function prepare_getBucketProps($bucket){
        # Run the request...
        $params = array('props' => 'true', 'keys' => 'false');
        $uri = $this->buildRestPath($bucket, NULL, NULL, $params);
        $http = $this->http( $uri, array(200) );
        
        # prepare the http request 
        $handle = $http->handle;
        $transport = $this;
        $http->handle = function( $response ) use ( $handle, $transport ){
            $handle( $response );
            $transport->parseObjectResponse( $response );
        };
        
        return $http;
    }

   /**
    * Serialize set bucket property request and deserialize response
    * bucket = bucket object
    * props = assoc array of properties
    * @return boolean
    */
    public function setBucketProps($bucket, $props){
        $this->prepare_setBucketProps( $bucket, $props )->send();
    }
    
    public function prepare_setBucketProps($bucket, $props){
        # Construct the URL, Headers, and Content...
        $uri = $this->buildRestPath($bucket);
        $request = $this->http( $uri, array(204) );
        $request->method = 'PUT';
        $request->headers = array('Content-Type: application/json');
        $request->post = json_encode(array("props"=>$props));
        return $request;
    }
    
    public function getBucketKeys( $bucket, $cb = NULL ){
        return $this->prepare_getBucketKeys( $bucket, $cb )->send()->result;
    }
    
    public function prepare_getBucketKeys( $bucket, $cb = NULL ){
        $params = array('props'=>'false','keys'=>'true');
        $uri = $this->buildRestPath( $bucket, NULL, NULL, $params);
        $http = $this->http( $uri, array(200) );
        # prepare the http request 
        $handle = $http->handle;
        $transport = $this;
        $http->handle = function( $response ) use ( $handle, $transport ){
            $handle( $response );
            $transport->parseObjectResponse( $response );
        };
        
        return $http;
    }

    public function indexSearch($bucket, $indexName, $indexType, $startOrExact = null, $end=NULL, $dedupe=false) {
        return  $this->prepare_indexSearch($bucket, $indexName, $indexType, $startOrExact, $end, $dedupe)->send()->result;
    }
    
    public function prepare_indexSearch($bucket, $indexName, $indexType, $startOrExact = null, $end=NULL, $dedupe=false) {
        $uri = $this->buildIndexPath($bucket, "{$indexName}_{$indexType}", $startOrExact, $end, NULL);
        $http = $this->http( $uri, array(200) );
        $handle = $http->handle;
        $transport = $this;
        $http->handle = function( $response ) use ( $handle, $transport ){
            $handle( $response );
            $transport->parseObjectResponse( $response );
        };
        
        return $http;
    }

   /**
    * Serialize map/reduce request
    */
    public function mapred($inputs, $query, $timeout = null){
        return $this->prepare_mapred($inputs, $query, $timeout)->send()->result;
    }
    
    public function prepare_mapred($inputs, $query, $timeout = null){
        # Construct the job, optionally set the timeout...
        $job = array("inputs"=>$inputs, "query"=>$query);
        if ($timeout != NULL) $job["timeout"] = $timeout;
        $http = $this->http("/" . $this->mapred_prefix, array(200) );
        $http->post = json_encode($job);
        $http->handle = function( $response ) {
            $result = json_decode($response->body);
            if( isset( $result->error ) ) {
                throw new Exception( $result->error );
            }
            $response->result = $result;
        };
        return $http;
    }

   /**
    * Set the client id. This overrides the default, random client id, which is automatically
    * generated when none is specified in when creating the transport object.
    */
    public function setClientId($client_id){
        $this->clientid = $client_id;
        return true;
    }

   /**
    * Fetch the client id for the transport.
    */
    public function getClientId(){
        return $this->clientid;
    }
    
    protected function parseLinks($linkHeaders) {
        $links = array();
        $linkHeaders = explode(",", trim($linkHeaders));
        foreach ($linkHeaders as $linkHeader) {
          $linkHeader = trim($linkHeader);
          $matches = array();
          $result = preg_match("/\<\/([^\/]+)\/([^\/]+)\/([^\/]+)\>; ?riaktag=\"([^\"]+)\"/", $linkHeader, $matches);
          if ($result == 1) {
            $links[] = new Link(urldecode($matches[2]), urldecode($matches[3]), urldecode($matches[4]));
          }
        }
        return $links;
      }
      
    function resultObject(){
        return (object) array(
            'vclock'=>null,
            'content_type'=>null,
            'key'=>null,
            'links'=> array(),
            'meta'=>array(),
            'indexes'=>array(),
            'data'=>null,
            'siblings'=>null,
            );
    }
    
    function parseObjectResponse($response) {
        
        $status = $response->http_code;
        
        if( $status == 404 ){
            return $response->result = NULL;
        }
        
        $result = $response->result;
        $headers = array();
        foreach (self::parseHttpHeaders($response->response_header) as $key=>$value) {
            $headers[strtolower($key)] = $value;
        }
        
        # set the content type
        if( isset( $headers['content-type'] ) )$result->content_type = $headers['content-type'];
        
        if( isset( $headers['x-riak-vclock'] ) ) {
            $result->vclock = $headers['x-riak-vclock'];
            unset( $headers['x-riak-vclock'] );
        }

        # Parse the link header...
        if (array_key_exists("link", $headers)) {
          $result->links = $this->parseLinks($headers["link"]);
        }
    
        # Parse the index and metadata headers
        foreach($headers as $key=>$val) {
          if (preg_match('~^x-riak-([^-]+)-(.+)$~', $key, $matches)) {
            switch($matches[1]) {
              case 'index':
                $index = substr($matches[2], 0, strrpos($matches[2], '_'));
                $type = substr($matches[2], strlen($index)+1);
                if ($type !== null) $index .= '_' . $type;
                $result->indexes[$index] = array_map('urldecode', explode(', ', $val));
                break;
              case 'meta':
                $result->meta[$matches[2]] = $val;
                break;
            }
          }
        }
    
        # If 300 (Siblings), then load the first sibling, and
        # store the rest.
        if ($status == 300) {
          $siblings = explode("\n", trim($response->body));
          array_shift($siblings); # Get rid of 'Siblings:' string.
          $result->siblings = $siblings;
          return $result;
        }
      
        if ($status == 201) {
          $path_parts = explode('/', $headers['location']);
          $result->key = array_pop($path_parts);
        }
        
    
        # Possibly json_decode...
        if (($status == 200 || $status == 201)) {
            $result->data = $response->body;
        }
    
        return $result;
  }

    
  /**
   * Convert this Riak\Link object to a link header string. Used internally.
   */
  function toLinkHeader($link) {
    return "</" .
      $this->prefix . "/" .
      urlencode($link->bucket) . "/" .
      urlencode($link->key) . ">; riaktag=\"" . 
      urlencode($link->getTag()) . "\"";
  }
  
    
    
    
      /**
   * Given a bucketname, Key, LinkSpec, and Params,
   * construct and return a URL.
   */
  protected function buildRestPath($bucket = NULL, $key=NULL, $spec=NULL, $params=NULL) {
    
    $path = '/' . $this->prefix;
    
    # Add '.../bucket'
    if (!is_null($bucket) ) {
      $path .= '/' . urlencode($bucket);
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
  
  protected function http( $uri, array $expected_statuses = NULL ){
    $http = new HttpRequest( 'http://' . $this->host . ':' . $this->port . $uri );
    $result = $this->resultObject();
    $http->build = function( $http ) use ( $result ){
        $http->response->result = $result;
    };
    
    $http->handle = function( $response ) use ( $expected_statuses ) {
        $status = $response->http_code;
        # Check if the server is down (status==0)
        if ($status == 0) {
          throw new Exception('Could not contact Riak Server!', $response);
        }
        
        # Verify that we got one of the expected statuses. Otherwise, throw an exception.
        if ($expected_statuses && !in_array($status, $expected_statuses)) {
          $m = 'Expected status ' . implode(' or ', $expected_statuses) . ', received ' . $status;
          throw new Exception($m, $response);
        }

    };
    return $http;
    
  }

  /**
   * Given a Riak\Client, Riak\Bucket, Key, LinkSpec, and Params,
   * construct and return a URL for searching secondary indexes.
   * @author Eric Stevens <estevens@taglabsinc.com>
   * @param Riak\Client $client
   * @param Riak\Bucket $bucket
   * @param string $index - Index Name & type (eg, "indexName_bin")
   * @param string|int $start - Starting value or exact match if no ending value
   * @param string|int $end - Ending value for range search
   * @param array $params - Any extra query parameters to pass on the URL
   * @return string URL
   */
  protected function buildIndexPath($bucket, $index, $start, $end=NULL, array $params=NULL) {
    
    # Build 'http://hostname:port/prefix/bucket'
    $path = array($this->indexPrefix);

    # Add '.../bucket'
    $path[] = urlencode($bucket);
    
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
    $path = implode('/', $path);

    # Add query parameters.
    if (!is_null($params)) {
        $path .= '?' . httprequest::buildQuery($params);
    }

    return '/' . $path;
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
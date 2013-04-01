<?php
namespace Riak\Transport;

/**
 * Allows us to run curl calls in a non-blocking fashion.
 */
class HttpPool {
    
   /**
    * @type array   list of running http requests
    */
    protected $requests = array();
    
  /**
    * callback triggers for handling the http response
    */
    protected $handlers = array();
    
    /**
	 * The curl multi handle.
	 */
	protected $resource = NULL;

	/**
	 * Initializes the curl multi request.
	 */
	public function __construct(){
		$this->resource = curl_multi_init();
	}
	
	/**
	* clean up the pool, removing any attached curl resources and close the multi.
	*/
	public function __destruct(){
		foreach ($this->requests as $i => $http){
			unset( $this->requests[ $i ] );
		    if( ! $http->resource ) continue;
			curl_multi_remove_handle($this->resource, $http->resource);
		}
        curl_multi_close($this->resource);
	}

    public function attach( \Closure $handler ){
        $this->handlers[] = $handler;
    }
    
    /**
    * when an http request is done, trigger any attached handlers.
    * if you want customized callbacks per request, you can attach a callback
    * to examine a local variable in the http object and perform a callback on that.
    */
    public function handle( HttpRequest $request ){
        foreach( $this->handlers as $handler ) $handler( $request );
    }
    
    /**
    * add a new request to the pool.
    */
    public function add( HttpRequest $request, array $opts = array() ){
        $ch = $request->build($opts);
        $this->requests[(int)$request->resource] = $request;
        curl_multi_add_handle($this->resource, $request->resource);
        return $request;
    }
    
    /**
    * get a list of all the requests in the pool.
    */
    public function requests(){
        return $this->requests;
    }

   /**
    * wait for the specified timeout for data to come back on the socket.
    */
	public function select($timeout = 1.0){
	    if( ! $this->poll() ) return FALSE;
        curl_multi_select($this->resource, $timeout);
		return $this->poll();
	}
	
	/**
	* process all of the requests in the pool.
	*/
	public function finish(){
		while ($this->select(1) === TRUE) { /* no op */ }
		return TRUE;
	}

	/**
	 * Polls (non-blocking) the curl requests for additional data.
	 *
	 * This function must be called periodically while processing other data.  This function is non-blocking
	 * and will return if there is no data ready for processing on any of the internal curl handles.
	 *
	 * @return boolean TRUE if there are transfers still running or FALSE if there is nothing left to do.
	 */
	public function poll(){
		$still_running = 0; // number of requests still running.
		do {
			$result = curl_multi_exec($this->resource, $still_running);
			if ($result != CURLM_OK) continue;
            do {
                $messages_in_queue = 0;
                $info = curl_multi_info_read($this->resource, $messages_in_queue);
                if( ! $info ) continue;
                if( !  isset($info['handle']) ) continue;
                if( ! isset($this->requests[(int)$info['handle']]) ) continue;
                curl_multi_remove_handle($this->resource, $info['handle']);
                $request = $this->requests[ (int) $info['handle'] ];
                unset( $this->requests[ (int) $info['handle'] ] );
                $request->handle();
                $this->handle( $request );
            }
            while($messages_in_queue > 0);
			
		}
		while ($result == CURLM_CALL_MULTI_PERFORM && $still_running > 0);

		// don't trust $still_running, as user may have added more urls
		// in callbacks
		return (boolean)$this->requests;
	}
    
}
// EOC

<?php
/**
 * The RiakClient object holds information necessary to connect to
 * Riak. The Riak API uses HTTP, so there is no persistent
 * connection, and the RiakClient object is extremely lightweight.
 * 
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class RiakClient
{
    /** @var string */
    private $host;
    
    /** @var integer */
    private $port;
    
    /** @var string */
    private $prefix;
    
    /** @var string */
    private $mapredPrefix;
    
    /** @var string */
    private $indexPrefix;
    
    /** @var string */
    private $clientId;
    
    /** 
     * How many replicas need to agree when retrieving an existing object 
     * before the write.
     * 
     * @var integer|null
     */
    private $r;
    
    /** 
     * How many replicas to write to before returning a successful response
     * 
     * @var integer|null
     */
    private $w;
    
    /** 
     * How many replicas to commit to durable storage before returning a 
     * successful response
     * 
     * @var integer|null
     */
    private $dw;
    
    /**
     * Construct a new RiakClient object.
     * 
     * @param string  $host         Hostname or IP address 
     *                              (default '127.0.0.1')
     * @param integer $port         Port number (default 8098)
     * @param string  $prefix       Interface prefix (default "riak")
     * @param string  $mapredPrefix MapReduce prefix (default "mapred")
     * 
     * @return void
     */
    public function __construct($host = '127.0.0.1', $port = 8098,
            $prefix = 'riak', $mapredPrefix = 'mapred') 
    {
        $this->host = $host;
        $this->port = $port;
        $this->prefix = $prefix;
        $this->mapredPrefix = $mapredPrefix;
        $this->indexPrefix = 'buckets';
        $this->clientId = 'php_' . base_convert(mt_rand(), 10, 36);
        $this->r = 2;
        $this->w = 2;
        $this->dw = 2;
    }

    /**
     * Get the R-value setting for this RiakClient. (default 2)
     * 
     * @return integer
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * Set the R-value for this RiakClient. This value will be used
     * for any calls to get(...) or getBinary(...) where where 1) no
     * R-value is specified in the method call and 2) no R-value has
     * been set in the RiakBucket.
     * 
     * @param integer $r The R value.
     * 
     * @return RiakClient
     */
    public function setR($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get the W-value setting for this RiakClient. (default 2)
     * 
     * @return integer
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set the W-value for this RiakClient. See setR(...) for a
     * description of how these values are used.
     * 
     * @param integer $w The W value.
     * 
     * @return RiakClient
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get the DW-value for this ClientOBject. (default 2)
     * 
     * @return integer
     */
    public function getDW()
    {
        return $this->dw;
    }

    /**
     * Set the DW-value for this RiakClient. See setR(...) for a
     * description of how these values are used.
     * 
     * @param integer $dw The DW value.
     * 
     * @return RiakClient
     */
    public function setDW($dw)
    {
        $this->dw = $dw;

        return $this;
    }

    /**
     * Get the clientID for this RiakClient.
     * 
     * @return string
     */
    public function getClientID()
    {
        return $this->clientId;
    }

    /**
     * Set the clientID for this RiakClient. Should not be called
     * unless you know what you are doing.
     * 
     * @param string $clientId The new clientId.
     * 
     * @return RiakClient
     */
    public function setClientID($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get the bucket by the specified name. Since buckets always exist,
     * this will always return a RiakBucket.
     * 
     * @param string $name Name
     * 
     * @return RiakBucket
     */
    public function bucket($name)
    {
        return new RiakBucket($this, $name);
    }

    /**
     * Get all buckets.
     * 
     * @return RiakBucket[]
     */
    public function buckets()
    {
        $url = RiakUtils::buildRestPath($this);
        $response = RiakUtils::httpRequest('GET', $url . '?buckets=true');
        $responseObj = json_decode($response[1]);
        $buckets = array();
        foreach ($responseObj->buckets as $name) {
            $buckets[] = $this->bucket($name);
        }

        return $buckets;
    }

    /**
     * Check if the Riak server for this RiakClient is alive.
     * 
     * @return boolean
     */
    public function isAlive()
    {
        $url = 'http://' . $this->host . ':' . $this->port . '/ping';
        $response = RiakUtils::httpRequest('GET', $url);

        return ($response != null) && ($response[1] == 'OK');
    }

    # MAP/REDUCE/LINK FUNCTIONS

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @return RiakMapReduce
     * @see RiakMapReduce::add()
     */
    public function add($params)
    {
        $mapReduce = new RiakMapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "add"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation. This command will
     * return an error unless executed against a Riak Search cluster.
     * 
     * @param mixed $params Parameters
     * 
     * @return RiakMapReduce
     * @see RiakMapReduce::search()
     */
    public function search($params)
    {
        $mapReduce = new RiakMapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "search"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @see RiakMapReduce::link()
     * @return void
     */
    public function link($params)
    {
        $mapReduce = new RiakMapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "link"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @see RiakMapReduce::map()
     * @return void
     */
    public function map($params)
    {
        $mapReduce = new RiakMapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "map"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @see RiakMapReduce::reduce()
     * @return void
     */
    public function reduce($params)
    {
        $mapReduce = new RiakMapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "reduce"), $args);
    }
}

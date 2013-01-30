<?php
namespace Basho\Riak;
use Basho\Riak\Bucket, Basho\Riak\MapReduce;
/**
 * The Client object holds information necessary to connect to
 * Riak. The Riak API uses HTTP, so there is no persistent
 * connection, and the Client object is extremely lightweight.
 * 
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class Client
{
    /** @var string */
    public $host;

    /** @var integer */
    public $port;

    /** @var string */
    public $prefix;

    /** @var string */
    public $mapredPrefix;

    /** @var string */
    public $indexPrefix;

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
     * Construct a new Client object.
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
     * Get the R-value setting for this Client. (default 2)
     * 
     * @return integer
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * Set the R-value for this Client. This value will be used
     * for any calls to get(...) or getBinary(...) where where 1) no
     * R-value is specified in the method call and 2) no R-value has
     * been set in the Bucket.
     * 
     * @param integer $r The R value.
     * 
     * @return Client
     */
    public function setR($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get the W-value setting for this Client. (default 2)
     * 
     * @return integer
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set the W-value for this Client. See setR(...) for a
     * description of how these values are used.
     * 
     * @param integer $w The W value.
     * 
     * @return Client
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
     * Set the DW-value for this Client. See setR(...) for a
     * description of how these values are used.
     * 
     * @param integer $dw The DW value.
     * 
     * @return Client
     */
    public function setDW($dw)
    {
        $this->dw = $dw;

        return $this;
    }

    /**
     * Get the clientID for this Client.
     * 
     * @return string
     */
    public function getClientID()
    {
        return $this->clientId;
    }

    /**
     * Set the clientID for this Client. Should not be called
     * unless you know what you are doing.
     * 
     * @param string $clientId The new clientId.
     * 
     * @return Client
     */
    public function setClientID($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get the bucket by the specified name. Since buckets always exist,
     * this will always return a Bucket.
     * 
     * @param string $name Name
     * 
     * @return Bucket
     */
    public function bucket($name)
    {
        return new Bucket($this, $name);
    }

    /**
     * Get all buckets.
     * 
     * @return Bucket[]
     */
    public function buckets()
    {
        $url = Utils::buildRestPath($this);
        $response = Utils::httpRequest('GET', $url . '?buckets=true');
        $responseObj = json_decode($response[1]);
        $buckets = array();
        foreach ($responseObj->buckets as $name) {
            $buckets[] = $this->bucket($name);
        }

        return $buckets;
    }

    /**
     * Check if the Riak server for this Client is alive.
     * 
     * @return boolean
     */
    public function isAlive()
    {
        $url = 'http://' . $this->host . ':' . $this->port . '/ping';
        $response = Utils::httpRequest('GET', $url);

        return ($response != null) && ($response[1] == 'OK');
    }

    # MAP/REDUCE/LINK FUNCTIONS

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @return MapReduce
     * @see MapReduce::add()
     */
    public function add($params)
    {
        $mapReduce = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "add"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation. This command will
     * return an error unless executed against a Riak Search cluster.
     * 
     * @param mixed $params Parameters
     * 
     * @return MapReduce
     * @see MapReduce::search()
     */
    public function search($params)
    {
        $mapReduce = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "search"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @see MapReduce::link()
     * @return void
     */
    public function link($params)
    {
        $mapReduce = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "link"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @see MapReduce::map()
     * @return void
     */
    public function map($params)
    {
        $mapReduce = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "map"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * 
     * @param mixed $params Parameters
     * 
     * @see MapReduce::reduce()
     * @return void
     */
    public function reduce($params)
    {
        $mapReduce = new MapReduce($this);
        $args = func_get_args();

        return call_user_func_array(array(&$mapReduce, "reduce"), $args);
    }
}

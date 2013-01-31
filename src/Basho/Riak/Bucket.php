<?php
namespace Basho\Riak;
use Basho\Riak\Link, Basho\Riak\Object, Basho\Riak\Utils;
/**
 * The Bucket object allows you to access and change information
 * about a Riak bucket, and provides methods to create or retrieve
 * objects within the bucket.
 * 
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class Bucket
{
    /** @var Client */
    private $client;

    /** @var string */
    public $name;

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
     * @param Client $client Client
     * @param string     $name   Name
     * 
     * @return void
     */
    public function __construct(Client $client, $name)
    {
        $this->client = $client;
        $this->name = $name;
        $this->r = null;
        $this->w = null;
        $this->dw = null;
    }

    /**
     * Get the bucket name.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the number of replicas needed to agree when retrieving an existing
     * object before the write for this bucket, if it is set, otherwise return
     * the number for the client.
     * 
     * @param integer $r Number of replicas need to agree
     * 
     * @return integer
     */
    public function getR($r = null)
    {
        if ($r != null) {
            return $r;
        }
        if ($this->r != null) {
            return $this->r;
        }
        return $this->client->getR();
    }

    /**
     * Set the the number replicas needed to agree when retrieving an existing object 
     * before the write for this bucket. get(...) and getBinary(...)
     * operations that do not specify an number will use this value.
     * 
     * @param integer $r Number of replicas need to agree
     * 
     * @return Bucket
     */
    public function setR($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get the W-value for this bucket, if it is set, otherwise return
     * the W-value for the client.
     * 
     * @param integer $w The W-value.
     * 
     * @return integer
     */
    public function getW($w)
    {
        if ($w != null) {
            return $w;
        }
        if ($this->w != null) {
            return $this->w;
        }
        return $this->client->getW();
    }

    /**
     * Set the W-value for this bucket. See setR(...) for more information.
     * 
     * @param integer $w The new W-value.
     * 
     * @return Bucket
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get the DW-value for this bucket, if it is set, otherwise return
     * the DW-value for the client.
     * 
     * @param integer $dw The DW-value
     * 
     * @return integer
     */
    public function getDW($dw)
    {
        if ($dw != null) {
            return $dw;
        }
        if ($this->dw != null) {
            return $this->dw;
        }
        return $this->client->getDW();
    }

    /**
     * Set the DW-value for this bucket. See setR(...) for more information.
     * 
     * @param integer $dw The new DW-value
     * 
     * @return Bucket
     */
    public function setDW($dw)
    {
        $this->dw = $dw;

        return $this;
    }

    /**
     * Create a new Riak object that will be stored as JSON.
     * 
     * @param string $key  Name of the key.
     * @param object $data The data to store. (default null)
     * 
     * @return Object
     */
    public function newObject($key, $data = null)
    {
        $obj = new Object($this->client, $this, $key);
        $obj->setData($data);
        $obj->setContentType('application/json');
        $obj->jsonize = true;

        return $obj;
    }

    /**
     * Create a new Riak object that will be stored as plain text/binary.
     * 
     * @param string $key         Name of the key.
     * @param object $data        The data to store.
     * @param string $contentType The content type of the object. 
     *                            (default 'application/json')
     * @return Object
     */
    public function newBinary($key, $data, $contentType = 'application/json')
    {
        $obj = new Object($this->client, $this, $key);
        $obj->setData($data);
        $obj->setContentType($contentType);
        $obj->jsonize = false;

        return $obj;
    }

    /**
     * Retrieve a JSON-encoded object from Riak.
     * 
     * @param string  $key Name of the key.
     * @param integer $r   R-Value of the request (defaults to bucket's R)
     * 
     * @return Object
     */
    public function get($key, $r = null)
    {
        $obj = new Object($this->client, $this, $key);
        $obj->jsonize = true;
        $r = $this->getR($r);

        return $obj->reload($r);
    }

    /**
     * Retrieve a binary/string object from Riak.
     * 
     * @param string  $key Name of the key.
     * @param integer $r   R-Value of the request (defaults to bucket's R)
     * 
     * @return Object
     */
    public function getBinary($key, $r = null)
    {
        $obj = new Object($this->client, $this, $key);
        $obj->jsonize = false;
        $r = $this->getR($r);

        return $obj->reload($r);
    }

    /**
     * Set the N-value for this bucket, which is the number of replicas
     * that will be written of each object in the bucket. Set this once
     * before you write any data to the bucket, and never change it
     * again, otherwise unpredictable things could happen. This should
     * only be used if you know what you are doing.
     * 
     * @param integer $nval The new N-Val.
     * 
     * @return void
     */
    public function setNVal($nval)
    {
        return $this->setProperty("n_val", $nval);
    }

    /**
     * Retrieve the N-value for this bucket.
     * 
     * @return integer
     */
    public function getNVal()
    {
        return $this->getProperty("n_val");
    }

    /**
     * If set to true, then writes with conflicting data will be stored
     * and returned to the client. This situation can be detected by
     * calling hasSiblings() and getSiblings(). This should only be used
     * if you know what you are doing.
     * 
     * @param boolean $bool True to store and return conflicting writes.
     * 
     * @return void
     */
    public function setAllowMultiples($bool)
    {
        return $this->setProperty("allow_mult", $bool);
    }

    /**
     * Retrieve the 'allow multiples' setting.
     * 
     * @return boolean
     */
    public function getAllowMultiples()
    {
        return "true" == $this->getProperty("allow_mult");
    }

    /**
     * Set a bucket property. This should only be used if you know what
     * you are doing.
     * 
     * @param string $key   Property to set.
     * @param mixed  $value Property value.
     * 
     * @return void
     */
    public function setProperty($key, $value)
    {
        return $this->setProperties(array($key => $value));
    }

    /**
     * Retrieve a bucket property.
     * 
     * @param  string $key The property to retrieve.
     * 
     * @return mixed
     */
    public function getProperty($key)
    {
        $props = $this->getProperties();
        if (array_key_exists($key, $props)) {
            return $props[$key];
        } else {
            return null;
        }
    }

    /**
     * Set multiple bucket properties in one call. This should only be
     * used if you know what you are doing.
     * 
     * @param array $props An associative array of $key=>$value.
     * 
     * @return void
     * @throws \Exception if http request was empty or not 204
     */
    public function setProperties($props)
    {
        # Construct the URL, Headers, and Content...
        $url = Utils::buildRestPath($this->client, $this);
        $headers = array('Content-Type: application/json');
        $content = json_encode(array("props" => $props));

        # Run the request...
        $response = Utils::httpRequest('PUT', $url, $headers, $content);

        # Handle the response...
        if ($response == null) {
            throw new \Exception("Error setting bucket properties.");
        }

        # Check the response value...
        $status = $response[0]['http_code'];
        if ($status != 204) {
            throw new \Exception("Error setting bucket properties.");
        }
    }

    /**
     * Retrieve an associative array of all bucket properties.
     * 
     * @return array
     * @throws \Exception if bucket properties could not be requested
     */
    public function getProperties()
    {
        //Run the request...
        $params = array('props' => 'true', 'keys' => 'false');
        $url = Utils::buildRestPath($this->client, $this, null, null,
                $params);
        $response = Utils::httpRequest('GET', $url);

        //Use a Object to interpret the response, 
        //we are just interested in the value.
        $obj = new Object($this->client, $this, null);
        $obj->populate($response, array(200));
        if (!$obj->exists()) {
            throw new \Exception("Error getting bucket properties.");
        }

        $props = $obj->getData();
        $props = $props["props"];

        return $props;
    }

    /**
     * Retrieve an array of all keys in this bucket.
     * Note: this operation is pretty slow.
     * 
     * @return array
     * @throws \Exception if bucket properties could not be requested
     */
    public function getKeys()
    {
        $params = array('props' => 'false', 'keys' => 'true');
        $url = Utils::buildRestPath($this->client, $this, null, null,
                $params);
        $response = Utils::httpRequest('GET', $url);

        //Use a Object to interpret the response, 
        //we are just interested in the value.
        $obj = new Object($this->client, $this, null);
        $obj->populate($response, array(200));
        if (!$obj->exists()) {
            throw new \Exception("Error getting bucket properties.");
        }
        $keys = $obj->getData();

        return array_map("urldecode", $keys["keys"]);
    }

    /**
     * Search a secondary index
     * 
     * @param string         $indexName    The name of the index to search
     * @param string         $indexType    The type of index ('int' or 'bin')
     * @param string|integer $startOrExact
     * @param string|integer $end          Optional.
     * @param boolean        $dedupe       Optional. Whether to eliminate 
     *                                     duplicate entries if any of 
     *                                     Links
     * @return array                    
     * @author Eric Stevens <estevens@taglabsinc.com>
     */
    public function indexSearch($indexName, $indexType, $startOrExact,
            $end = null, $dedupe = false)
    {
        $url = Utils::buildIndexPath($this->client, $this,
                "{$indexName}_{$indexType}", $startOrExact, $end);
        $response = Utils::httpRequest('GET', $url);

        $obj = new Object($this->client, $this, null);
        $obj->populate($response, array(200));
        if (!$obj->exists()) {
            throw new \Exception("Error searching index.");
        }
        $data = $obj->getData();
        $keys = array_map("urldecode", $data["keys"]);

        $seenKeys = array();
        foreach ($keys as $id => &$key) {
            if ($dedupe) {
                if (isset($seenKeys[$key])) {
                    unset($keys[$id]);
                    continue;
                }
                $seenKeys[$key] = true;
            }
            $key = new Link($this->name, $key);
            $key->client = $this->client;
        }
        return $keys;
    }
}

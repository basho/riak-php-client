<?php
/**
 * This file is part of the riak-php-client.
 *
 * PHP version 5.3+
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link https://github.com/localgod/riak-php-client
 */
namespace Basho\Riak;

/**
 * The Bucket object allows you to access and change information
 * about a Riak bucket and provides methods to create or retrieve
 * objects within the bucket.
 *
 * @method integer getR()
 * @method integer getW()
 * @method integer getDW()
 * @method Client getClient()
 * @method string getName()
 * @method Bucket setR()
 * @method Bucket setW()
 * @method Bucket setDW()
 * @method Bucket setClient()
 * @method Bucket setName()
 */
class Bucket
{
    /**
     * A riak client object
     * @var Client
     */
    private $client;

    /**
     * The name of the bucket
     * @var string
     */
    public $name;

    /**
     * How many replicas need to agree when retrieving an existing object
     * before the write.
     *
     * If null the number for the client will be used. get(...)
     * and getBinary(...) operations that do not specify an number will
     * use this value.
     *
     * @var integer|null
     */
    private $r;

    /**
     * How many replicas to write to before returning a successful response.
     * If null the number for the client will be used.
     *
     * @var integer|null
     */
    private $w;

    /**
     * How many replicas to commit to durable storage before returning a
     * successful response.  If null the number for the client will be used.
     *
     * @var integer|null
     */
    private $dw;

    /**
     * Construct a new bucket.
     *
     * @param Client $client Client
     * @param string $name   The name of the bucket
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
     * Call method by name
     *
     * @param string $name Name of method to call
     * @param array $arguments Arguments to method
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^get.+/', $name)) {
            return $this->callGet($name, $arguments);
        } else {
            if (preg_match('/^set.+/', $name)) {
                return $this->callSet($name, $arguments);
            }
        }
    }

    /**
     * Get property of the bucket
     *
     * @param string $name Name of method to call
     * @param array  $arguments Arguments to method
     *
     * @return mixed
     */
    private final function callGet($name, $arguments)
    {
        $propertyName = lcfirst(str_replace('get', '', $name));
        switch (lcfirst($propertyName)) {
            case 'r':
                $value = $this->r != null ? $this->r : $this->client->getR();
                break;
            case 'w':
                $value = $this->w != null ? $this->w : $this->client->getW();
                break;
            case 'dw':
                $value = $this->dw != null ? $this->dw : $this->client->getDW();
                break;
            case 'dW':
                $value = $this->dw != null ? $this->dw : $this->client->getDW();
                break;
            case 'client':
                $value = $this->client;
                break;
            case 'name':
                $value = $this->name;
                break;
            default:
                throw new \InvalidArgumentException('The property \'' . lcfirst($propertyName) . '\' dos not exists.');
        }

        return $value;
    }

    /**
     * Call set method
     *
     * @param string $name Name of method to call
     * @param array $arguments Arguments to method
     *
     * @throws \InvalidArgumentException if unknown property is requested
     * @return \Basho\Riak\Client
     */
    private final function callSet($name, $arguments)
    {
        $propertyName = lcfirst(str_replace('set', '', $name));
        switch (lcfirst($propertyName)) {
            case 'r':
                $this->r = $arguments[0];
                break;
            case 'w':
                $this->w = $arguments[0];
                break;
            case 'dw':
                $this->dw = $arguments[0];
                break;
            case 'dW':
                $this->dw = $arguments[0];
                break;
            case 'client':
                $this->client = $arguments[0];
                break;
            case 'name':
                $this->name = $arguments[0];
                break;
            default:
                throw new \InvalidArgumentException('The property \'' . lcfirst($propertyName) . '\' dos not exists.');
        }
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
     * Get properties object for this bucket
     *
     * @return \Basho\Riak\Properties
     */
    public function getProperties()
    {
        return new Properties($this->client, $this);
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
        $url = Utils::buildRestPath($this->client, $this, null, null, $params);
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
     *
     * @return array
     * @author Eric Stevens <estevens@taglabsinc.com>
     * @throws \Exception if requested object does not exist
     */
    public function indexSearch($indexName, $indexType, $startOrExact, $end = null, $dedupe = false)
    {
        $url = Utils::buildIndexPath($this->client, $this, "{$indexName}_{$indexType}", $startOrExact, $end);
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

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
 * The Properties object allows you to access and change information
 * about a Riak buckets properties.
 *
 * Generally you should only use the set* methods if you know what you are doing.
 */
class Properties
{
    /**
     * A riak client object
     * @var Client
     */
    private $client;

    /**
     * A riak bucket object
     * @var Bucket
     */
    private $bucket;

    /**
     * Create a new properties object
     *
     * @param Client $client A riak client
     * @param Bucket $bucket A riak bucket
     *
     * @return void
     */
    public function __construct(Client $client, Bucket $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
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
        $this->setProperty("n_val", $nval);
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
       $this->setProperty("allow_mult", $bool);
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
        $this->setProperties(array($key => $value));
    }

    /**
     * Retrieve a bucket property.
     *
     * @param string $key The property to retrieve.
     *
     * @return mixed
     */
    public function getProperty($key)
    {
        $props = self::getProperties();
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
     * @param array $properties An associative array of $key=>$value.
     *
     * @return void
     * @throws \Exception if http request was empty or not 204
     */
    public function setProperties(array $properties)
    {
        //Construct the URL, Headers, and Content...
        $url = Utils::buildRestPath($this->client, $this->bucket);
        $headers = array('Content-Type: application/json');
        $content = json_encode(array("props" => $properties));

        //Run the request...
        $response = Utils::httpRequest('PUT', $url, $headers, $content);

        //Handle the response...
        if ($response == null) {
            throw new \Exception("Error setting bucket properties.");
        }

        //Check the response value...
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
        $url = Utils::buildRestPath($this->client, $this->bucket, null, null, $params);
        $response = Utils::httpRequest('GET', $url);

        //Use a Object to interpret the response,
        //we are just interested in the value.
        $obj = new Object($this->client, $this->bucket, null);
        $obj->populate($response, array(200));
        if (!$obj->exists()) {
            throw new \Exception("Error getting bucket properties.");
        }

        $props = $obj->getData();
        $props = $props["props"];

        return $props;
    }
}

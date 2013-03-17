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
 * The Client object holds information necessary to connect to
 * Riak. The Riak API uses HTTP, so there is no persistent
 * connection, and the Client object is extremely lightweight.
 *
 * @method integer getR()
 * @method integer getW()
 * @method integer getDW()
 * @method string getId()
 * @method string getHost()
 * @method integer getPort()
 * @method string getPrefix()
 * @method string getMapredPrefix()
 * @method string getIndexPrefix()
 * @method Client setR($r)
 * @method Client setW($w)
 * @method Client setDW($dw)
 * @method Client setId($Id) Should not be called unless you know what you are doing.
 * @method Client setHost($host)
 * @method Client setPort($port)
 * @method Client setPrefix($prefix)
 * @method Client setMapredPrefix($mapredPrefix)
 * @method Client setIndexPrefix($indexPrefix)
 * @method MapReduce add($params)
 * @method MapReduce link($params)
 * @method MapReduce search($params)
 * @method MapReduce map($params)
 * @method MapReduce reduce($params)
 *
 */
class Client
{
    /**
     * Hostname or IP address (default '127.0.0.1')
     * @var string
     */
    private $host;

    /**
     * Port number (default 8098)
     * @var integer
     */
    private $port;

    /**
     * Interface prefix (default "riak")
     * @var string
     */
    private $prefix;

    /**
     * MapReduce prefix (default "mapred")
     * @var string
     */
    private $mapredPrefix;

    /**
     * Index prefix (default "buckets")
     * @var string
     */
    private $indexPrefix;

    /**
     * Clinet id
     * @var string
     */
    private $id;

    /**
     * How many replicas need to agree when retrieving an existing object
     * before the write. If not set default is 2.
     *
     * @var integer|null
     */
    private $r;

    /**
     * How many replicas to write to before returning a successful response.
     *
     * If not set default is 2.
     *
     * @var integer|null
     */
    private $w;

    /**
     * How many replicas to commit to durable storage before returning a
     * successful response
     *
     * If not set default is 2.
     *
     * @var integer|null
     */
    private $dw;

    /**
     * Construct a new Client object.
     *
     * @param string $host          Hostname or IP address
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
        $this->setHost($host)
            ->setPort($port)
            ->setPrefix($prefix)
            ->setMapredPrefix($mapredPrefix)
            ->setIndexPrefix('buckets')
            ->setId('php_' . base_convert(mt_rand(), 10, 36))
            ->setR(2)
            ->setW(2)
            ->setDW(2);
    }

    /**
     * Call method by name
     *
     * @param string $name Name of method to call
     * @param array $arguments Arguments to method
     *
     * @internal Used internally.
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^get.+/', $name)) {
            return $this->callGet($name, $arguments);
        } else if (preg_match('/^set.+/', $name)) {
            return $this->callSet($name, $arguments);
        } else if (preg_match('/^add$/', $name)) {
            return $this->mapReduce(MapReduce::ADD, $arguments);
        } else if (preg_match('/^search$/', $name)) {
            return $this->mapReduce(MapReduce::SEARCH, $arguments);
        } else if (preg_match('/^link$/', $name)) {
            return $this->mapReduce(MapReduce::LINK, $arguments);
        } else if (preg_match('/^map$/', $name)) {
            return $this->mapReduce(MapReduce::MAP, $arguments);
        } else if (preg_match('/^reduce$/', $name)) {
            return $this->mapReduce(MapReduce::REDUCE, $arguments);
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
        $reflect = new \ReflectionClass(__CLASS__);

        foreach ($reflect->getProperties() as $property) {
            if ($property->name == $propertyName) {
                return $this->$propertyName;
            }
            if ("dW" == $propertyName) {
                return $this->dw;
            }
        }
    }

    /**
     * Call set method
     *
     * @param string $name Name of method to call
     * @param array $arguments Arguments to method
     *
     * @return Client
     */
    private final function callSet($name, $arguments)
    {
        $propertyName = lcfirst(str_replace('set', '', $name));
        $reflect = new \ReflectionClass(__CLASS__);

        foreach ($reflect->getProperties() as $property) {
            if ($property->name == $propertyName) {
                $this->$propertyName = $arguments[0];;
            }
            if ("dW" == $propertyName) {
                $this->dw = $arguments[0];
            }
        }
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
        $url = 'http://' . $this->getHost() . ':' . $this->getPort() . '/ping';
        $response = Utils::httpRequest('GET', $url);

        return ($response != null) && ($response[1] == 'OK');
    }

    /**
     * Preforme Map/Reduce operation.
     *
     * @param mixed $params Parameters
     *
     * @return MapReduce
     * @see MapReduce
     */
    private function mapReduce($operation, $params)
    {
        $mapReduce = new MapReduce($this);
        return call_user_func_array(array(&$mapReduce, $operation), $params);
    }
}

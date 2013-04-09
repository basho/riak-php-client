<?php
/**
 * Riak PHP Client
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Apache License, Version 2.0 that is
 * bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to <eng@basho.com> so we can send you a copy immediately.
 *
 * @category   Riak
 * @package    Riak
 * @copyright  Copyright (c) 2013 Basho Technologies, Inc. and contributors.
 */
namespace Basho\Riak;

use Basho\Riak\Bucket,
    Basho\Riak\MapReduce,
    Basho\Riak\Utils;

/**
 * Riak
 *
 * @category   Riak
 * @package    Riak
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class Riak
{
    protected $hosts = array();
    protected $dropped = array();
    protected $cluster = false;
    public $host = '127.0.0.1';

    /**
     * Construct a new Client object.
     * @param string|array $hosts - Hostname or IP address or hostname list (default '127.0.0.1')
     * @param int $port - Port number (default 8098)
     * @param string $prefix - Interface prefix (default "riak")
     * @param string $mapred_prefix - MapReduce prefix (default "mapred")
     */
    public function __construct($hosts = '127.0.0.1', $port = 8098, $prefix = 'riak', $mapred_prefix = 'mapred')
    {
        $this->hosts = (array)$hosts;
        $this->host = $this->hosts[0];
        $this->cluster = count($this->hosts) > 1;
        $this->port = $port;
        $this->prefix = $prefix;
        $this->mapred_prefix = $mapred_prefix;
        $this->indexPrefix = 'buckets';
        $this->clientid = 'php_' . base_convert(mt_rand(), 10, 36);
        $this->r = 2;
        $this->w = 2;
        $this->dw = 2;
    }

    /**
     * Get the R-value setting for this Client. (default 2)
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
     * @param integer $r - The R value.
     * @return $this
     */
    public function setR($r)
    {
        $this->r = $r;
        return $this;
    }

    /**
     * Get the W-value setting for this Client. (default 2)
     * @return integer
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set the W-value for this Client. See setR(...) for a
     * description of how these values are used.
     * @param integer $w - The W value.
     * @return $this
     */
    public function setW($w)
    {
        $this->w = $w;
        return $this;
    }

    /**
     * Get the DW-value for this ClientOBject. (default 2)
     * @return integer
     */
    public function getDW()
    {
        return $this->dw;
    }

    /**
     * Set the DW-value for this Client. See setR(...) for a
     * description of how these values are used.
     * @param  integer $dw - The DW value.
     * @return $this
     */
    public function setDW($dw)
    {
        $this->dw = $dw;
        return $this;
    }

    /**
     * Get the clientID for this Client.
     * @return string
     */
    public function getClientID()
    {
        return $this->clientid;
    }

    /**
     * Set the clientID for this Client. Should not be called
     * unless you know what you are doing.
     * @param string $clientID - The new clientID.
     * @return $this
     */
    public function setClientID($clientid)
    {
        $this->clientid = $clientid;
        return $this;
    }

    /**
     * Get the bucket by the specified name. Since buckets always exist,
     * this will always return a Bucket.
     * @return Bucket
     */
    public function bucket($name)
    {
        return new Bucket($this, $name);
    }

    /**
     * Get all buckets.
     * @return array() of Bucket objects
     */
    public function buckets()
    {
        $url = Utils::buildRestPath($this);
        $response = $this->httpRequest('GET', $url . '?buckets=true');
        $response_obj = json_decode($response[1]);
        $buckets = array();
        foreach ($response_obj->buckets as $name) {
            $buckets[] = $this->bucket($name);
        }
        return $buckets;
    }

    /**
     * Check if the Riak server for this Client is alive.
     * @return boolean
     */
    public function isAlive()
    {
        $response = $this->httpRequest('GET', '/ping');
        return ($response != NULL) && ($response[1] == 'OK');
    }

    /**
     * Wrapper over \Basho\Riak\Utils::httpRequest to support fast switching in case of dying host
     * @see \Basho\Riak\Utils::httpRequest()
     */
    public function httpRequest($method, $url, $request_headers = array(), $obj = '')
    {
        # Each request for a new server for load balancing
        if ($this->cluster) {
            shuffle($this->hosts);
            $this->host = $this->hosts[0];
        }
        # The first request
        $response = Utils::httpRequest($method, 'http://' . $this->host . ':' . $this->port . $url, $request_headers, $obj);
        # If no response is received then try to switch to another
        if (($response == NULL || $response[0]['http_code'] == 0) && $this->changeHost()) {
            $response = Utils::httpRequest($method, 'http://' . $this->host . ':' . $this->port . $url, $request_headers, $obj);
        }
        return $response;
    }

    /**
     * Search live host and switch to it.
     * @return boolean
     */
    private function changeHost() {
        if (!$this->cluster) {
            return false;
        }
        # drop host
        $this->dropped[] = $this->host;
        unset($this->hosts[0]);
        # If there are no hosts, check previously disabled in search of available
        if (empty($this->hosts)) {
            foreach ($this->dropped as $k => $host) {
                $response = Utils::httpRequest('GET', 'http://' . $host . ':' . $this->port . '/ping');
                if ($response != NULL && $response[1] == 'OK') {
                    $this->hosts[] = $host;
                    unset($this->dropped[$k]);
                }
            }
        }
        # switch to another host
        if (!empty($this->hosts)) {
            while ($this->hosts) {
                shuffle($this->hosts);
                $response = Utils::httpRequest('GET', 'http://' . $this->hosts[0] . ':' . $this->port . '/ping');
                if ($response != NULL && $response[1] == 'OK') {
                    $this->host = $this->hosts[0];
                    return true;
                }
                # host is not available. lock it
                $this->dropped[] = $this->hosts[0];
                unset($this->hosts[0]);
            }
        }
        return false;
    }


    # MAP/REDUCE/LINK FUNCTIONS

    /**
     * Start assembling a Map/Reduce operation.
     * @see MapReduce::add()
     * @return MapReduce
     */
    public function add($params)
    {
        $mr = new MapReduce($this);
        $args = func_get_args();
        return call_user_func_array(array(&$mr, "add"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation. This command will
     * return an error unless executed against a Riak Search cluster.
     * @see MapReduce::search()
     * @return MapReduce
     */
    public function search($params)
    {
        $mr = new MapReduce($this);
        $args = func_get_args();
        return call_user_func_array(array(&$mr, "search"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * @see MapReduce::link()
     */
    public function link($params)
    {
        $mr = new MapReduce($this);
        $args = func_get_args();
        return call_user_func_array(array(&$mr, "link"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * @see MapReduce::map()
     */
    public function map($params)
    {
        $mr = new MapReduce($this);
        $args = func_get_args();
        return call_user_func_array(array(&$mr, "map"), $args);
    }

    /**
     * Start assembling a Map/Reduce operation.
     * @see MapReduce::reduce()
     */
    public function reduce($params)
    {
        $mr = new MapReduce($this);
        $args = func_get_args();
        return call_user_func_array(array(&$mr, "reduce"), $args);
    }
}
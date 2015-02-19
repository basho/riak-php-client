<?php

/*
Copyright 2014 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Basho\Riak\Api;

use Basho\Riak\Api;
use Basho\Riak\ApiInterface;
use Basho\Riak\Command;
use Basho\Riak\Location;
use Basho\Riak\Node;

/**
 * Class Http
 *
 * Handles communications between end user app & Riak via Riak HTTP API using cURL
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Http extends Api implements ApiInterface
{
    /**
     * cURL connection handle
     *
     * @var null
     */
    protected static $connection = null;

    /**
     * API path
     *
     * @var string
     */
    protected $path = '';

    /**
     * Query string
     *
     * @var string
     */
    protected $query = '';

    public function resetConnection()
    {
        curl_reset(self::$connection);
    }

    public function closeConnection()
    {
        curl_close(self::$connection);
        self::$connection = null;
    }

    /**
     * Prepare request to be sent
     *
     * @param Command $command
     * @param Node    $node
     * @return $this
     */
    public function prepare(Command $command, Node $node)
    {
        // call parent prepare method to setup object members
        parent::prepare($command, $node);

        // set the API path to be used
        $this->buildPath();

        // general connection preparation
        $this->prepareConnection();

        // request specific connection preparation
        $this->prepareRequest();

        // set the request string to be sent
        $this->request = curl_getinfo($this->getConnection(), CURLINFO_HEADER_OUT);

        return $this;
    }

    /**
     * Sets the API path for the command
     *
     * @return $this
     */
    protected function buildPath()
    {
        $bucket = NULL;
        $key = '';

        $bucket = $this->getCommand()->getBucket();

        if (method_exists($this->command, 'getLocation')) {
            $location = $this->getCommand()->getLocation();
            if (!empty($location) && $location instanceof Location) {
                $key = $location->getKey();
            }
        }

        switch (get_class($this->getCommand())) {
            case 'Basho\Riak\Command\Bucket\List':
                $this->path = sprintf('/types/%s/buckets/%s', $bucket->getType(), $bucket->getName());
                break;
            case 'Basho\Riak\Command\Bucket\Fetch':
            case 'Basho\Riak\Command\Bucket\Store':
            case 'Basho\Riak\Command\Bucket\Reset':
                $this->path = sprintf('/types/%s/buckets/%s/props', $bucket->getType(), $bucket->getName());
                break;
            case 'Basho\Riak\Command\Bucket\Keys':
                $this->path = sprintf('/types/%s/buckets/%s/keys', $bucket->getType(), $bucket->getName());
                break;
            case 'Basho\Riak\Command\Object\Fetch':
            case 'Basho\Riak\Command\Object\Store':
            case 'Basho\Riak\Command\Object\Delete':
                $this->path = sprintf('/types/%s/buckets/%s/keys/%s', $bucket->getType(), $bucket->getName(), $key);
                break;
            case 'Basho\Riak\Command\DateType\Fetch':
            case 'Basho\Riak\Command\DateType\Store':
                // curl -XPOST "127.0.0.1:8098/types/counters/buckets/default/datatypes/mycounter?returnbody=true" -i -H "Content-Type:application/json" -d '1'
                $this->path = sprintf('/types/%s/buckets/%s/datatypes/%s', $bucket->getType(), $bucket->getName(), $key);
                break;
            default:
                $this->path = '';
        }

        return $this;
    }

    /**
     * Prepare Connection
     *
     * Sets general connection options that are used with every request
     *
     * @return $this
     */
    protected function prepareConnection()
    {
        // set the response body to be returned
        curl_setopt($this->getConnection(), CURLOPT_RETURNTRANSFER, 1);

        // record outgoing headers
        curl_setopt($this->getConnection(), CURLINFO_HEADER_OUT, true);

        // return incoming headers
        curl_setopt($this->getConnection(), CURLOPT_HEADER, true);

        return $this;
    }

    /**
     * @return resource
     */
    public function getConnection()
    {
        if (!self::$connection) {
            $this->openConnection();
        }

        return self::$connection;
    }

    public function openConnection()
    {
        self::$connection = curl_init();
    }

    /**
     * Prepare request
     *
     * Sets connection options that are specific to this request
     *
     * @return $this
     */
    protected function prepareRequest()
    {
        return $this->prepareRequestMethod()
                    ->prepareRequestParameters()
                    ->prepareRequestUrl();
    }

    /**
     * Prepares the complete request URL
     *
     * @return $this
     */
    protected function prepareRequestUrl()
    {
        $url = sprintf('%s%s?%s', $this->node->getUri(), $this->path, $this->query);

        // set the built request URL on the connection
        curl_setopt($this->getConnection(), CURLOPT_URL, $url);

        return $this;
    }

    /**
     * Prepare request parameters
     *
     * @return $this
     */
    protected function prepareRequestParameters()
    {
        if ($this->getCommand()->hasParameters()) {
            // if POST or PUT, add parameters to post data, else add to uri
            if (in_array($this->getCommand()->getMethod(), ['POST', 'PUT'])) {
                curl_setopt($this->getConnection(), CURLOPT_POSTFIELDS, $this->getCommand()->getParameters());
            } else {
                // build query using RFC 3986 (spaces become %20 instead of '+')
                $this->query = http_build_query($this->getCommand()->getParameters(), '', '&', PHP_QUERY_RFC3986);
            }
        }

        return $this;
    }

    /**
     * Prepare the request method
     *
     * @return $this
     */
    protected function prepareRequestMethod()
    {
        switch ($this->getCommand()->getMethod()) {
            case "POST":
                curl_setopt($this->getConnection(), CURLOPT_POST, true);
                break;
            case "PUT":
                curl_setopt($this->getConnection(), CURLOPT_PUT, true);
                break;
            case "DELETE":
                curl_setopt($this->getConnection(), CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case "HEAD":
                curl_setopt($this->getConnection(), CURLOPT_NOBODY, true);
                break;
            default:
                // reset http method to get in case its changed
                curl_setopt($this->getConnection(), CURLOPT_HTTPGET, true);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function send()
    {
        // set the response header and body callback functions
        curl_setopt($this->getConnection(), CURLOPT_HEADERFUNCTION, [$this, 'responseHeaderCallback']);
        curl_setopt($this->getConnection(), CURLOPT_WRITEFUNCTION, [$this, 'responseBodyCallback']);

        // execute the request
        $this->success = curl_exec($this->getConnection());
        if ($this->success === FALSE) {
            $this->error = curl_error($this->getConnection());
        }

        // set the response http code
        $this->statusCode = curl_getinfo($this->getConnection(), CURLINFO_HTTP_CODE);

        return $this->success;
    }

    /**
     * Response header callback
     *
     * Handles callback from curl when the response is received, it parses the headers into an array sets them as
     * member of the class.
     *
     * Has to be public for curl to be able to access it.
     *
     * @param $ch
     * @param $header
     * @return int
     */
    public function responseHeaderCallback($ch, $header)
    {
        var_dump($header);
        if (strpos($header, ':')) {
            list ($key, $value) = explode(':', $header);

            $value = trim($value);

            if (!empty($value)) {
                if (!isset($this->responseHeaders[$key])) {
                    $this->responseHeaders[$key] = $value;
                } elseif (is_array($this->responseHeaders[$key])) {
                    $this->responseHeaders[$key] = array_merge($this->responseHeaders[$key], [$value]);
                } else {
                    $this->responseHeaders[$key] = array_merge([$this->responseHeaders[$key]], [$value]);
                }
            }
        }

        return strlen($header);
    }

    /**
     * Response body callback
     *
     * Handles callback from curl when the response is received, it sets the response body as a member of the class.
     *
     * Has to be public for curl to be able to access it.
     *
     * @param $ch
     * @param $body
     * @return int
     */
    public function responseBodyCallback($ch, $body)
    {
        $this->responseBody = $body;

        return strlen($body);
    }
}
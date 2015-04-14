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
use Basho\Riak\Bucket;
use Basho\Riak\Command;
use Basho\Riak\Exception;
use Basho\Riak\Location;
use Basho\Riak\Node;

/**
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
    protected $connection = null;

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

    private $options = [];

    public function closeConnection()
    {
        curl_close($this->connection);
        $this->connection = null;
    }

    /**
     * Prepare request to be sent
     *
     * @param Command $command
     * @param Node $node
     *
     * @return $this
     */
    public function prepare(Command $command, Node $node)
    {
        if ($this->connection) {
            $this->resetConnection();
        }

        // call parent prepare method to setup object members
        parent::prepare($command, $node);

        // set the API path to be used
        $this->buildPath();

        // general connection preparation
        $this->prepareConnection();

        // request specific connection preparation
        $this->prepareRequest();

        return $this;
    }

    public function resetConnection()
    {
        $this->command = null;
        $this->options = [];
        $this->path = '';
        $this->query = '';
        $this->requestBody = '';
        $this->responseHeaders = [];
        $this->responseBody = '';

        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            curl_reset($this->connection);
        } else {
            curl_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Sets the API path for the command
     *
     * @return $this
     */
    protected function buildPath()
    {
        $bucket = null;
        $key = '';

        $bucket = $this->command->getBucket();

        $location = $this->command->getLocation();
        if (!empty($location) && $location instanceof Location) {
            $key = $location->getKey();
        }
        switch (get_class($this->command)) {
            case 'Basho\Riak\Command\Bucket\List':
                $this->path = sprintf('/types/%s/buckets/%s', $bucket->getType(), $bucket->getName());
                break;
            case 'Basho\Riak\Command\Bucket\Fetch':
            case 'Basho\Riak\Command\Bucket\Store':
            case 'Basho\Riak\Command\Bucket\Delete':
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
            case 'Basho\Riak\Command\DataType\Counter\Fetch':
            case 'Basho\Riak\Command\DataType\Counter\Store':
            case 'Basho\Riak\Command\DataType\Set\Fetch':
            case 'Basho\Riak\Command\DataType\Set\Store':
            case 'Basho\Riak\Command\DataType\Map\Fetch':
            case 'Basho\Riak\Command\DataType\Map\Store':
            $this->path = sprintf('/types/%s/buckets/%s/datatypes/%s', $bucket->getType(), $bucket->getName(),
                $key);
                break;
            case 'Basho\Riak\Command\Search\Index\Fetch':
            case 'Basho\Riak\Command\Search\Index\Store':
            case 'Basho\Riak\Command\Search\Index\Delete':
                $this->path = sprintf('/search/index/%s', $this->command);
                break;
            case 'Basho\Riak\Command\Search\Schema\Fetch':
            case 'Basho\Riak\Command\Search\Schema\Store':
                $this->path = sprintf('/search/schema/%s', $this->command);
                break;
            case 'Basho\Riak\Command\Search\Fetch':
                $this->path = sprintf('/search/query/%s', $this->command);
                break;
            case 'Basho\Riak\Command\MapReduce\Fetch':
                $this->path = sprintf('/%s', $this->config['mapred_prefix']);
                break;
            case 'Basho\Riak\Command\Indexes\Query':
                $this->path = $this->createIndexQueryPath($bucket);
                break;
            default:
                $this->path = '';
        }

        return $this;
    }


    /**
     * Generates the URL path for a 2i Query
     *
     * @param Bucket $bucket
     * @return string
     * @throws Exception if 2i query is invalid.
     */
    private function createIndexQueryPath(Bucket $bucket)
    {
        /**  @var Command\Indexes\Query $command */
        $command = $this->command;

        if($command->isMatchQuery()) {
            $path =  sprintf('/types/%s/buckets/%s/index/%s/%s', $bucket->getType(),
                        $bucket->getName(),
                        $command->getIndexName(),
                        $command->getMatchValue());
        }
        elseif($command->isRangeQuery()) {
            $path =  sprintf('/types/%s/buckets/%s/index/%s/%s/%s', $bucket->getType(),
                        $bucket->getName(),
                        $command->getIndexName(),
                        $command->getLowerBound(),
                        $command->getUpperBound());
        }
        else
        {
            throw new Exception("Invalid Secondary Index Query.");
        }

        return $path;
    }

    /**
     * Prepare Connection
     *
     * Sets general connection options that are used with every request
     *
     * @return $this
     * @throws Exception
     */
    protected function prepareConnection()
    {
        // record outgoing headers
        $this->options[CURLINFO_HEADER_OUT] = 1;

        if ($this->node->useTls()) {
            // CA File
            if ($this->node->getCaFile()) {
                $this->options[CURLOPT_CAINFO] = $this->node->getCaFile();
            } elseif ($this->node->getCaDirectory()) {
                $this->options[CURLOPT_CAPATH] = $this->node->getCaDirectory();
            } else {
                throw new Exception('A Certificate Authority file is required for secure connections.');
            }

            // verify CA file
            $this->options[CURLOPT_SSL_VERIFYPEER] = true;

            // verify host common name
            $this->options[CURLOPT_SSL_VERIFYHOST] = 0;

            if ($this->node->getUserName()) {
                $this->options[CURLOPT_USERPWD] = sprintf('%s:%s', $this->node->getUserName(),
                    $this->node->getPassword());
            } elseif ($this->node->getCertificate()) {
                /*
                 * NOT CURRENTLY SUPPORTED ON HTTP
                 *
                $this->options[CURLOPT_SSLCERT] = $this->node->getCertificate();
                $this->options[CURLOPT_SSLCERTTYPE] = 'P12';
                if ($this->node->getCertificatePassword()) {
                    $this->options[CURLOPT_SSLCERTPASSWD] = $this->node->getCertificatePassword();
                }
                if ($this->node->getPrivateKey()) {
                    $this->options[CURLOPT_SSLKEY] = $this->node->getPrivateKey();
                    if ($this->node->getPrivateKeyPassword()) {
                        $this->options[CURLOPT_SSLKEYPASSWD] = $this->node->getPrivateKeyPassword();
                    }
                }
                */
            }
        }

        return $this;
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
            ->prepareRequestHeaders()
            ->prepareRequestParameters()
            ->prepareRequestUrl()
            ->prepareRequestData();
    }

    /**
     * Prepare request data
     *
     * @return $this
     */
    protected function prepareRequestData()
    {
        // if POST or PUT, add parameters to post data, else add to uri
        if (in_array($this->command->getMethod(), ['POST', 'PUT'])) {
            $this->requestBody = $this->command->getEncodedData();
            $this->options[CURLOPT_POSTFIELDS] = $this->requestBody;
        }

        return $this;
    }

    /**
     * Prepares the complete request URL
     *
     * @return $this
     */
    protected function prepareRequestUrl()
    {
        $protocol = $this->node->useTls() ? 'https' : 'http';
        $url = sprintf('%s://%s%s?%s', $protocol, $this->node->getUri(), $this->path, $this->query);

        // set the built request URL on the connection
        $this->options[CURLOPT_URL] = $url;

        return $this;
    }

    /**
     * Prepare request parameters
     *
     * @return $this
     */
    protected function prepareRequestParameters()
    {
        if ($this->command->hasParameters()) {
            // build query using RFC 3986 (spaces become %20 instead of '+')
            $this->query = http_build_query($this->command->getParameters(), '', '&', PHP_QUERY_RFC3986);
        }

        return $this;
    }

    /**
     * Prepares the request headers
     *
     * @return $this
     */
    protected function prepareRequestHeaders()
    {
        $curl_headers = [];

        // getHeaders() Headers are expected in the following format:
        // [[key, value], [key, value]...]
        foreach ($this->command->getHeaders() as $key => $value) {
            $curl_headers[] = sprintf('%s: %s', $value[0], $value[1]);
        }

        // set the request headers on the connection
        $this->options[CURLOPT_HTTPHEADER] = $curl_headers;

        return $this;
    }

    /**
     * Prepare the request method
     *
     * @return $this
     */
    protected function prepareRequestMethod()
    {
        switch ($this->command->getMethod()) {
            case "POST":
                $this->options[CURLOPT_POST] = 1;
                break;
            case "PUT":
                $this->options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
            case "DELETE":
                $this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
            case "HEAD":
                $this->options[CURLOPT_NOBODY] = 1;
                break;
            default:
                // reset http method to get in case its changed
                $this->options[CURLOPT_HTTPGET] = 1;
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
        $this->options[CURLOPT_HEADERFUNCTION] = [$this, 'responseHeaderCallback'];
        $this->options[CURLOPT_WRITEFUNCTION] = [$this, 'responseBodyCallback'];

        if ($this->command->isVerbose()) {
            // set curls output to be the output buffer stream
            $this->options[CURLOPT_STDERR] = fopen('php://stdout', 'w+');
            $this->options[CURLOPT_VERBOSE] = 1;

            // there is a bug when verbose is enabled, header out causes no output
            // @see https://bugs.php.net/bug.php?id=65348
            unset($this->options[CURLINFO_HEADER_OUT]);
        }

        // set all options on the resource
        curl_setopt_array($this->getConnection(), $this->options);

        // execute the request
        $this->success = curl_exec($this->getConnection());
        if ($this->success === false) {
            $this->error = curl_error($this->getConnection());
        }

        $this->request = curl_getinfo($this->getConnection(), CURLINFO_HEADER_OUT);

        // set the response http code
        $this->statusCode = curl_getinfo($this->getConnection(), CURLINFO_HTTP_CODE);

        return $this->success;
    }

    /**
     * @return resource
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->openConnection();
        }

        return $this->connection;
    }

    public function openConnection()
    {
        $this->connection = curl_init();

        return $this;
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
     *
     * @return int
     */
    public function responseHeaderCallback($ch, $header)
    {
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
     *
     * @return int
     */
    public function responseBodyCallback($ch, $body)
    {
        $this->responseBody .= $body;

        return strlen($body);
    }
}
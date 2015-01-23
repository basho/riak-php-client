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

namespace Basho\Riak;

/**
 * Class Api
 *
 * [summary]
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Api
{
    /**
     * Request string to be sent
     *
     * @var string
     */
    protected $request = '';

    /**
     * Response headers returned from request
     *
     * @var array
     */
    protected $responseHeaders = [];

    /**
     * Response body returned from request
     *
     * @var string
     */
    protected $responseBody = '';

    /**
     * HTTP Code of response
     *
     * @var int
     */
    protected $httpCode = 0;

    /**
     * [short description]
     *
     * @var Command|null
     */
    protected $command = null;

    /**
     * [short description]
     *
     * @var Node|null
     */
    protected $node = null;

    protected $clientId = '';

    public function __construct($clientId)
    {
        $this->setClientId($clientId);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode
     */
    protected function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @return Command|null
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param Command|null $command
     * @return $this
     */
    protected function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return Node|null
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param Node|null $node
     * @return $this
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $request
     * @return $this
     */
    protected function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param string $responseBody
     * @return $this
     */
    public function setResponseBody($responseBody)
    {
        $this->responseBody = $responseBody;

        return $this;
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @param array $responseHeaders
     * @return $this
     */
    public function setResponseHeaders($responseHeaders)
    {
        $this->responseHeaders = $responseHeaders;

        return $this;
    }

    /**
     * Prepare the api connection
     *
     * @param Command $command
     * @param Node    $node
     * @return $this
     */
    public function prepare(Command $command, Node $node)
    {
        $this->setCommand($command);
        $this->setNode($node);

        return $this;
    }
}
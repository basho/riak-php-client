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
 * Extend this class to implement your own API bridge.
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
     * Request body to be sent
     *
     * @var string
     */
    protected $requestBody = '';

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
     * HTTP Status Code from response
     *
     * @var int
     */
    protected $statusCode = 0;

    /**
     * @var Command|null
     */
    protected $command = null;

    /**
     * @var Node|null
     */
    protected $node = null;

    protected $success = null;

    protected $error = '';

    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
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
     *
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
     *
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
        return $this->request . $this->requestBody;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * Prepare the api connection
     *
     * @param Command $command
     * @param Node $node
     *
     * @return $this
     */
    public function prepare(Command $command, Node $node)
    {
        $this->setCommand($command);
        $this->setNode($node);

        return $this;
    }

    /**
     * send
     *
     * @return Command\Response
     */
    abstract public function send();
}
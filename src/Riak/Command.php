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

use Basho\Riak\Api\Response;
use Basho\Riak\Command\Builder;

/**
 * Class Command
 *
 * The command class is used to build a read or write command to be executed against a Riak node.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Command
{
    /**
     * Request method
     *
     * This can be GET, POST, PUT, or DELETE
     *
     * @see http://docs.basho.com/riak/latest/dev/references/http/
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var DataType|null
     */
    protected $dataType = null;

    /**
     * Command parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * @var Response|null
     */
    protected $response = NULL;

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param $key string
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Command has parameters?
     *
     * @return bool
     */
    public function hasParameters()
    {
        return (bool)count($this->parameters);
    }

    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return DataType|null
     */
    public function getDataType()
    {
        return $this->dataType;
    }
}
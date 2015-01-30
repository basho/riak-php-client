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
    CONST FETCH_OBJECT = 'Object\\Fetch';
    CONST STORE_OBJECT = 'Object\\Store';
    CONST DELETE_OBJECT = 'Object\\Delete';

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
     * @var Bucket|null
     */
    protected $bucket = null;

    /**
     * @var Object|null
     */
    protected $object = null;

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

    public static function builder()
    {
        return new Builder(new static());
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
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
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

    /**
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param Bucket $bucket
     * @return $this
     */
    public function setBucket(Bucket $bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * @return Object|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param \Basho\Riak\Object $object
     * @return $this
     */
    public function setObject(Object $object)
    {
        $this->object = $object;

        return $this;
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

    /**
     * @param DataType|null $dataType
     *
     * @return $this
     */
    public function setDataType(DataType $dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Parses the response from the Riak Node based on the operation performed for the command executed
     *
     * @param int $statusCode
     * @param array $headers
     * @param string $body
     */
    abstract public function parseResponse($statusCode, array $headers, $body = '');

    /**
     * Validate command
     *
     * Method validates if the command has been built with the parameters / objects required to successfully execute.
     *
     * @return bool
     * @throws Builder\Exception
     */
    abstract public function validate();

    protected function required($objectName)
    {
        $method = "get{$objectName}";
        if (!$this->$method() && !$this->$method() instanceof $objectName) {
            throw new Builder\Exception("This command requires {$objectName} be defined.");
        }
    }
}
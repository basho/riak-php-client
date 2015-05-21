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

use Basho\Riak\Api\Translators\SecondaryIndexHeaderTranslator;
use Basho\Riak\Link;
use Basho\Riak\MapReduce;

/**
 * Main class for data objects in Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Object
{
    use HeadersTrait;

    /**
     * Stored data or object
     *
     * @var mixed|null
     */
    protected $data = null;

    protected $indexes = [];

    /**
     * @param mixed|null $data
     * @param array|null $headers
     */
    public function __construct($data = null, $headers = null)
    {
        $this->data = $data;

        if (empty($headers) || !is_array($headers)) {
            return;
        }

        $translator = new SecondaryIndexHeaderTranslator();
        $this->indexes = $translator->extractIndexesFromHeaders($headers);

        $this->headers = $headers;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getContentType()
    {
        return $this->getHeader('Content-Type');
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function getIndex($indexName)
    {
        return isset($this->indexes[$indexName]) ? $this->indexes[$indexName] : null;
    }

    public function addValueToIndex($indexName, $value)
    {
        $this->validateIndexNameAndValue($indexName, $value);

        if (!isset($this->indexes[$indexName])) {
            $this->indexes[$indexName] = [];
        }

        $this->indexes[$indexName][] = $value;

        return $this;
    }

    private function validateIndexNameAndValue($indexName, $value)
    {
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException("Invalid index type for '" . $indexName .
                "'index. Expecting '*_int' for an integer index, or '*_bin' for a string index.");
        }

        $isIntIndex = SecondaryIndexHeaderTranslator::isIntIndex($indexName);
        $isStringIndex = SecondaryIndexHeaderTranslator::isStringIndex($indexName);

        if (!$isIntIndex && !$isStringIndex) {
            throw new \InvalidArgumentException("Invalid index type for '" . $indexName .
                "'index. Expecting '*_int' for an integer index, or '*_bin' for a string index.");
        }

        if ($isIntIndex && !is_int($value)) {
            throw new \InvalidArgumentException("Invalid type for '" . $indexName .
                "'index. Expecting 'integer', value was '" . gettype($value) . "''");
        }

        if ($isStringIndex && !is_string($value)) {
            throw new \InvalidArgumentException("Invalid type for '" . $indexName .
                "'index. Expecting 'string', value was '" . gettype($value) . "''");
        }
    }

    public function removeValueFromIndex($indexName, $value)
    {
        if (!isset($this->indexes[$indexName])) {
            return $this;
        }

        $valuePos = array_search($value, $this->indexes[$indexName]);

        if ($valuePos !== false) {
            array_splice($this->indexes[$indexName], $valuePos, 1);
        }

        if(count($this->indexes[$indexName]) == 0) {
            unset($this->indexes[$indexName]);
        }

        return $this;
    }

}
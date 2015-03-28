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

use Basho\Riak\Link;
use Basho\Riak\MapReduce;

/**
 * Abstract Class DataType
 *
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

    protected $indexes = null;

    /**
     * @param mixed|null $data
     * @param array|null $headers
     */
    public function __construct($data = null, $headers = null)
    {
        $this->data = $data;

        if (!empty($headers) && is_array($headers)) {
            $this->headers = $headers;
        }
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
        $this->lazyInitIndexes();

        return $this->indexes;
    }

    public function getIndex($indexName)
    {
        $this->lazyInitIndexes();

        return $this->indexes[$indexName];
    }

    public function addValueToIndex($indexName, $value)
    {
        $this->lazyInitIndexes();

        if (!in_array($indexName, $this->indexes)) {
            $this->indexes[$indexName] = [];
        }

        $this->indexes[$indexName][] = $value;
    }

    public function removeValueFromIndex($indexName, $value)
    {
        $this->lazyInitIndexes();

        if(!in_array($indexName, $this->indexes)) {
            return;
        }

        if(($key = array_search($value, $this->indexes[$indexName])) !== false) {
            array_splice($this->indexes[$indexName], $key, 1);
        }
    }

    private function lazyInitIndexes()
    {
        if (is_null($this->indexes)) {
            $translator = new SecondaryIndexHeaderTranslator();
            $this->indexes = $translator->extractIndexes($this->getHeaders());
        }
    }
}
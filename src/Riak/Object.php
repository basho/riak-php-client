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

use Basho\Riak;
use Basho\Riak\Api\Http\Translators\SecondaryIndexTranslator;

/**
 * Main class for data objects in Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Object
{
    /**
     * Stored data or object
     *
     * @var mixed|null
     */
    protected $data = null;

    protected $indexes = [];

    protected $vclock = '';

    protected $content_type = 'text/plain';

    protected $content_encoding = 'utf-8';

    protected $charset = 'utf-8';

    protected $metadata = [];

    /**
     * @param mixed|null $data
     * @param array|null $headers DEPRECATED
     */
    public function __construct($data = null, $headers = [])
    {
        $this->data = $data;

        if (empty($headers) || !is_array($headers)) {
            return;
        }

        $translator = new SecondaryIndexTranslator();
        $this->indexes = $translator->extractIndexesFromHeaders($headers);

        // to prevent breaking the interface, parse $headers and place important stuff in new home
        if (!empty($headers[Riak\Api\Http::CONTENT_TYPE_KEY])) {
            // if charset is defined within the Content-Type header
            if (strpos($headers[Riak\Api\Http::CONTENT_TYPE_KEY], 'charset')) {
                $parts = explode(';', trim($headers[Riak\Api\Http::CONTENT_TYPE_KEY]));
                $this->content_type = $parts[0];
                $this->charset = trim(strrpos($parts[1], '='));
            } else {
                $this->content_type = $headers[Riak\Api\Http::CONTENT_TYPE_KEY];
            }
        }

        if (!empty($headers[Riak\Api\Http::VCLOCK_KEY])) {
            $this->content_type = $headers[Riak\Api\Http::VCLOCK_KEY];
        }

        // pull out metadata headers
        foreach($headers as $key => $value) {
            if (strpos($key, Riak\Api\Http::METADATA_PREFIX) !== false) {
                $this->metadata[substr($key, strlen(Riak\Api\Http::METADATA_PREFIX))] = $value;
            }
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
        return $this->content_type;
    }

    /**
     * @param string $content_type
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->content_encoding;
    }

    /**
     * @param string $content_encoding
     */
    public function setContentEncoding($content_encoding)
    {
        $this->content_encoding = $content_encoding;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getVclock()
    {
        return $this->vclock;
    }

    /**
     * @param string $vclock
     */
    public function setVclock($vclock)
    {
        $this->vclock = $vclock;
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

        $isIntIndex = SecondaryIndexTranslator::isIntIndex($indexName);
        $isStringIndex = SecondaryIndexTranslator::isStringIndex($indexName);

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

        if (count($this->indexes[$indexName]) == 0) {
            unset($this->indexes[$indexName]);
        }

        return $this;
    }

    public function setMetaDataValue($key, $value = '')
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    public function getMetaDataValue($key)
    {
        return $this->metadata[$key];
    }

    public function removeMetaDataValue($key)
    {
        unset($this->metadata[$key]);
        return $this;
    }

    public function getMetaData()
    {
        return $this->metadata;
    }
}

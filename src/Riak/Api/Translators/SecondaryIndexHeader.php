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

namespace Basho\Riak\Api\Translators;


/**
 * Class SecondaryIndexHeader
 * @package Basho\Riak\Api\Translators
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class SecondaryIndexHeader
{
    private $integerIndexSuffix = "_int";
    private $stringIndexSuffix = "_bin";
    private $indexSuffixLen = 4;
    private $indexHeaderPrefix = "x-riak-index-";
    private $indexHeaderPrefixLen = 13;

    public function extractIndexes($headers)
    {
        $indexes = [];

        foreach ($headers as $key => $value) {
            $this->parseIndexHeader($indexes, $key, $value);
        }

        return $indexes;
    }

    public function createHeaders($indexes)
    {
        $headers = [];

        foreach ($indexes as $indexName => $values) {
            $this->createIndexHeader($headers, $indexName, $values);
        }

        return $headers;
    }

    private function parseIndexHeader(&$indexes, $key, $rawValue)
    {
        if (!$this->isIndexHeader($key)) {
            return;
        }

        $indexName = $this->getIndexNameWithType($key);
        $value = $this->getIndexValue($indexName, $rawValue);

        $indexes[$indexName] = $value;
    }

    private function isIndexHeader($headerKey)
    {
        if (strlen($headerKey) <= $this->indexHeaderPrefixLen) {
            return false;
        }

        return substr_compare($headerKey, $this->indexHeaderPrefix, 0, $this->indexHeaderPrefixLen) == 0;
    }

    private function isStringIndex($headerKey)
    {
        $nameLen = strlen($headerKey) - $this->indexSuffixLen;

        return substr_compare($headerKey, $this->stringIndexSuffix, $nameLen) == 0;
    }

    private function getIndexNameWithType($key)
    {
        return substr($key, $this->indexHeaderPrefixLen);
    }

    private function getIndexValue($indexName, $value)
    {
        $values = explode(", ", $value);

        if ($this->isStringIndex($indexName)) {
            return $values;
        } else {
            return array_map("intval", $values);
        }
    }

    private function createIndexHeader(&$headers, $indexName, $values)
    {
        $headerKey = $this->indexHeaderPrefix . $indexName;
        foreach ($values as $indexName => $value) {
            $headers[] = [$headerKey, $value];
        }
    }
}
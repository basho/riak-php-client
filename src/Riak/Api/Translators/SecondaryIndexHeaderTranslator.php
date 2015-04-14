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
 * @author Alex Moore <amoore at basho d0t com>
 */
class SecondaryIndexHeaderTranslator
{
    const INT_INDEX_SUFFIX = '_int';
    const STR_IDX_SUFFIX = '_bin';
    const IDX_SUFFIX_LEN = 4;
    const IDX_HEADER_PREFIX = "x-riak-index-";
    const IDX_HEADER_PREFIX_LEN = 13;

    public static function isIntIndex($headerKey)
    {
        return static::indexNameContainsTypeSuffix($headerKey, self::INT_INDEX_SUFFIX);
    }

    public function extractIndexesFromHeaders(&$headers)
    {
        $indexes = [];

        foreach ($headers as $key => $value) {

            if (!$this->isIndexHeader($key)) {
                continue;
            }

            $this->parseIndexHeader($indexes, $key, $value);
            unset($headers[$key]);
        }

        return $indexes;
    }

    public static function isIndexHeader($headerKey)
    {
        if (strlen($headerKey) <= self::IDX_HEADER_PREFIX_LEN) {
            return false;
        }

        return substr_compare($headerKey, self::IDX_HEADER_PREFIX, 0, self::IDX_HEADER_PREFIX_LEN) == 0;
    }

    private function parseIndexHeader(&$indexes, $key, $rawValue)
    {
        $indexName = $this->getIndexNameWithType($key);
        $value = $this->getIndexValue($indexName, $rawValue);

        $indexes[$indexName] = $value;
    }

    private function getIndexNameWithType($key)
    {
        return substr($key, self::IDX_HEADER_PREFIX_LEN);
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

    public static function isStringIndex($headerKey)
    {
        return static::indexNameContainsTypeSuffix($headerKey, self::STR_IDX_SUFFIX);
    }

    private static function indexNameContainsTypeSuffix($indexName, $typeSuffix)
    {
        $nameLen = strlen($indexName) - self::IDX_SUFFIX_LEN;

        return substr_compare($indexName, $typeSuffix, $nameLen) == 0;
    }

    public function createHeadersFromIndexes($indexes)
    {
        $headers = [];

        foreach ($indexes as $indexName => $values) {
            $this->createIndexHeader($headers, $indexName, $values);
        }

        return $headers;
    }

    private function createIndexHeader(&$headers, $indexName, $values)
    {
        $headerKey = self::IDX_HEADER_PREFIX. $indexName;
        foreach ($values as $indexName => $value) {
            $headers[] = [$headerKey, $value];
        }
    }
}
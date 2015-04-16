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
 * Core data structure for a Riak Bucket.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Bucket
{
    /**
     * The default bucket type in Riak.
     */
    const DEFAULT_TYPE = "default";

    /**
     * Bucket properties
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Name of bucket
     */
    protected $name = '';

    /**
     * Buckets are grouped by type, inheriting the properties defined on the type
     */
    protected $type = '';

    /**
     * @param        $name
     * @param string $type
     * @param array $properties
     */
    public function __construct($name, $type = self::DEFAULT_TYPE, array $properties = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->properties = $properties;
    }

    public function __toString()
    {
        return $this->getNamespace();
    }

    /**
     * Bucket namespace
     *
     * This is a human readable namespace for the bucket.
     *
     * @return string
     */
    public function getNamespace()
    {
        return "/{$this->type}/{$this->name}/";
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getProperty($key)
    {
        $properties = $this->getProperties();
        if (isset($properties[$key])) {
            return $properties[$key];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
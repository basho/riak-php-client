<?php

/*
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
 * Class Bucket
 *
 * Core data structure for a Riak Bucket.
 *
 * @package     Basho\Riak
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class Bucket
{
    /**
     * Bucket properties
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Name of bucket
     *
     * @var string
     */
    protected $name = '';

    /**
     * Bucket type
     *
     * Buckets can be grouped together by type, inheriting the properties defined on the type
     *
     * @var string
     */
    protected $type = '';

    public function __construct($name = '', $type = '')
    {
        $this->setName($name);
        $this->setType($type);
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * If properties are not already loaded, fetch them from Riak
     *
     * @return array
     */
    public function getProperties()
    {
        if (!$this->properties) {
            // TODO: Fetch properties from Riak

            // TODO: Set property result to properties member
        }

        return $this->properties;
    }

    /**
     * getProperty
     *
     * @param $key
     * @return null
     */
    public function getProperty($key)
    {
        $properties = $this->getProperties();
        if (!empty($properties[$key])) {
            return $properties[$key];
        }

        return null;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        // TODO: If there is a difference, store it in Riak
        // qualify the difference using array_diff_assoc

        $this->properties = $properties;
    }

    /**
     * Bucket namespace
     *
     * This is a human readable namespace for a bucket.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->getType() . '\\' . $this->getName();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
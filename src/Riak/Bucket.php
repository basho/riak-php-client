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

use Basho\Riak\Bucket\Properties;

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
     * Bucket properties object
     *
     * @var Properties
     */
    protected $properties = null;

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

    public function __construct()
    {
        // initialize an empty properties object
        $this->properties = new Properties();
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
     * @return Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Properties $properties
     */
    public function setProperties($properties)
    {
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
<?php

/*
Copyright 2015 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Basho\Riak\Command\Object;

use Basho\Riak\Location;
use Basho\Riak\Object;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\Object[]
     */
    protected $objects = [];

    protected $location = null;

    public function __construct($success = true, $code = 0, $message = '', $location = null, $objects = [])
    {
        parent::__construct($success, $code, $message);

        $this->objects = $objects;
        $this->location = $location;
    }

    /**
     * @return bool
     */
    public function hasSiblings()
    {
        return count($this->objects) > 1;
    }

    /**
     * Retrieves the Location value from the response headers
     *
     * @return Location
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return \Basho\Riak\Object|null
     */
    public function getObject()
    {
        return !empty($this->objects[0]) ? $this->objects[0] : null;
    }

    /**
     * Fetches the sibling tags from the response
     *
     * @return array
     */
    public function getSiblings()
    {
        return $this->objects;
    }
}

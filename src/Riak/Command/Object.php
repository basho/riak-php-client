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

namespace Basho\Riak\Command;

use Basho\Riak\Bucket;
use Basho\Riak\Command;
use Basho\Riak\Location;

/**
 * Class Object
 *
 * Base class for Commands performing operations on Kv Objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Object extends Command
{
    /**
     * @var \Basho\Riak\Object|null
     */
    protected $object = NULL;

    /**
     * @var Bucket|null
     */
    protected $bucket = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    public function getObject()
    {
        return $this->object;
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
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

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Bucket;
use Basho\Riak\Location;

/**
 * Allows easy code sharing for Location getters / setters within the Command Builders
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait LocationTrait
{
    // location depends on bucket
    use BucketTrait;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    /**
     * @return Location|null
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param $key
     * @param $name
     * @param string $type
     *
     * @return $this
     */
    public function buildLocation($key, $name, $type = 'default')
    {
        $this->bucket = new Bucket($name, $type);
        $this->location = new Location($key, $this->bucket);

        return $this;
    }

    /**
     * @param Location $location
     *
     * @return $this
     */
    public function atLocation(Location $location)
    {
        $this->bucket = $location->getBucket();
        $this->location = $location;

        return $this;
    }
}
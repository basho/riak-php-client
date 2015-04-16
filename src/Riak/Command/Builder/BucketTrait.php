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

/**
 * Allows easy code sharing for Bucket getters / setters within the Command Builders
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait BucketTrait
{
    /**
     * Stores the Bucket object
     *
     * @var Bucket|null
     */
    protected $bucket = NULL;

    /**
     * Gets the Bucket object
     *
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Build a Bucket object to be added to the Command
     *
     * @param $name
     * @param string $type
     *
     * @return $this
     */
    public function buildBucket($name, $type = 'default')
    {
        $this->bucket = new Bucket($name, $type);

        return $this;
    }

    /**
     * Attach the provided Bucket to the Command
     *
     * @param Bucket $bucket
     *
     * @return $this
     */
    public function inBucket(Bucket $bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }
}
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
 * Class IndexTrait
 *
 * Allows easy code sharing for Bucket getters / setters within the Command Builders
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
trait IndexTrait
{
    /**
     * Stores the index name
     *
     * @var string|null
     */
    protected $indexName = NULL;

    protected $lowerBound = NULL;

    protected $upperBound = NULL;

    /**
     * Gets the index name
     *
     * @return string|null
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return string|null
     */
    public function getQueryValue()
    {
        if(is_null($this->upperBound)) {
            return $this->lowerBound;
        }
        else {
            return [$this->lowerBound, $this->upperBound];
        }
    }

    /**
     * Adds the index name information to the Command
     *
     * @param $indexName
     *
     * @return $this
     */
    public function withIndexName($indexName)
    {
        $this->indexName = $indexName;
        return $this;
    }


    /**
     * Adds the scalar index query information to the Command
     *
     * @param $value
     *
     * @return $this
     */
    public function withScalarValue($value)
    {
        $this->lowerBound = $value;
        return $this;
    }


    /**
     * Adds the range index query information to the Command
     *
     * @param $lowerBound
     * @param $upperBound
     *
     * @return $this
     */
    public function withRangeValue($lowerBound, $upperBound)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;

        return $this;
    }
}
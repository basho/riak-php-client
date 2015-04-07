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

namespace Basho\Riak\Command\Indexes;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Class Query
 *
 * Riak 2i query information.
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class Query extends Command\Object implements CommandInterface
{
    /**
     * @var string
     */
    protected $indexName = NULL;

    protected $lowerBound = NULL;

    protected $upperBound = NULL;

    public function __construct(Command\Builder\QueryIndex $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->indexName = $builder->getIndexName();
        $queryValue = $builder->getQueryValue();

        if(is_array($queryValue)) {
            $this->lowerBound = $queryValue[0];
            $this->upperBound = $queryValue[1];
        }
        else {
            $this->lowerBound = $queryValue;
        }

        //TODO Add optional parameters to $this->parameters;
    }

    public function getIndexName() {
        return $this->indexName;
    }

    public function getIndexLowerBound() {
        return $this->lowerBound;
    }

    public function getIndexUpperBound() {
        return $this->upperBound;
    }

    public function isRangeQuery()
    {
        return $this->upperBound != NULL;
    }

    /**
     * @return Command\Indexes\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($statusCode, $responseHeaders = [], $responseBody = '')
    {
        $this->response = new Command\Indexes\Response($statusCode, $responseHeaders, $responseBody);

        return $this;
    }

    /**
     * @return Command\Indexes\Response
     */
    public function execute()
    {
        return parent::execute();
    }


}
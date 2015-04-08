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

    protected $match = NULL;
    protected $lowerBound = NULL;
    protected $upperBound = NULL;

    protected $continuation = NULL; //Binary
    protected $maxResults; // Int
    protected $returnTerms; // Bool
    protected $paginationSort; // Bool
    protected $termFilter; // string
    protected $timeout; // timeout

    public function __construct(Command\Builder\QueryIndex $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->indexName = $builder->getIndexName();

        if($builder->isRangeQuery()) {
            $this->lowerBound = $builder->getLowerBound();
            $this->upperBound = $builder->getUpperBound();
        }
        else {
            $this->match = $builder->getMatchValue();
        }

        $this->continuation = $builder->getContinuation();
        $this->maxResults = $builder->getMaxResults();
        $this->returnTerms = $builder->getReturnTerms();
        $this->paginationSort = $builder->getPaginationSort();
        $this->termFilter = $builder->getTermFilter();
        $this->timeout = $builder->getTimeout();
    }

    public function getIndexName() {
        return $this->indexName;
    }

    public function getMatchValue() {
        return $this->match;
    }

    public function getLowerBound() {
        return $this->lowerBound;
    }

    public function getUpperBound() {
        return $this->upperBound;
    }

    public function isMatchQuery()
    {
        return isset($this->match);
    }

    public function isRangeQuery()
    {
        return isset($this->lowerBound) && isset($this->upperBound);
    }

    /**
     * @return null|string
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * @return int|null
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return bool|null
     */
    public function getReturnTerms()
    {
        return $this->returnTerms;
    }

    /**
     * @return bool|null
     */
    public function getPaginationSort()
    {
        return $this->paginationSort;
    }

    /**
     * @return null|string
     */
    public function getTermFilter()
    {
        return $this->termFilter;
    }

    /**
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

        /**
     * @return Command\Indexes\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getParameters()
    {
        $parameters = [];

        if(isset($this->continuation)) {
            $parameters["continuation"] = $this->continuation;
        }

        if(isset($this->maxResults)) {
            $parameters["max_results"] = $this->maxResults;
        }

        if(isset($this->returnTerms)) {
            $parameters["return_terms"] = $this->returnTerms;
        }

        if(isset($this->paginationSort)) {
            $parameters["pagination_sort"] = $this->paginationSort;
        }

        if(isset($this->termFilter)) {
            $parameters["term_regex"] = $this->termFilter;
        }

        if(isset($this->timeout)) {
            $parameters["timeout"] = $this->timeout;
        }

        return $parameters;
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
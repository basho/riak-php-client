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

namespace Basho\Riak\Command\Builder\Search;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Builds the command to fetch a collection of objects from Riak using Yokozuna search
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchObjects extends Command\Builder implements Command\BuilderInterface
{
    protected $default_field = '';

    protected $default_operation = '';

    protected $index_name = '';

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);

        $this->parameters['wt'] = 'json';
        $this->parameters['rows'] = 10;
        $this->parameters['start'] = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('IndexName');
        $this->required('Query');
        $this->required('MaxRows');
        $this->required('StartRow');
    }

    public function withIndexName($name)
    {
        $this->index_name = $name;

        return $this;
    }

    public function getIndexName()
    {
        return $this->index_name;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->parameters['q'];
    }

    /**
     * @return int
     */
    public function getMaxRows()
    {
        return $this->parameters['rows'];
    }

    /**
     * @return int
     */
    public function getStartRow()
    {
        return $this->parameters['start'];
    }

    /**
     * @return string
     */
    public function getFilterQuery()
    {
        return $this->parameters['fq'];
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->parameters['sort'];
    }

    /**
     * @return string
     */
    public function getDefaultField()
    {
        return $this->default_field;
    }

    /**
     * @return string
     */
    public function getDefaultOperation()
    {
        return $this->default_operation;
    }

    /**
     * @return string
     */
    public function getReturnFields()
    {
        return $this->parameters['fl'];
    }

    public function withQuery($query)
    {
        $this->parameters['q'] = $query;

        return $this;
    }

    public function withMaxRows($rows)
    {
        $this->parameters['rows'] = $rows;

        return $this;
    }

    public function withStartRow($row_num)
    {
        $this->parameters['start'] = $row_num;

        return $this;
    }

    public function withSortField($field_name)
    {
        $this->parameters['sort'] = $field_name;

        return $this;
    }

    public function withFilterQuery($filter_query)
    {
        $this->parameters['fq'] = $filter_query;

        return $this;
    }

    public function withDefaultField($default_field)
    {
        $this->default_field = $default_field;

        return $this;
    }

    public function withDefaultOperation($default_operation)
    {
        $this->default_operation = $default_operation;

        return $this;
    }

    public function withReturnFields($return_fields)
    {
        $this->parameters['fl'] = $return_fields;

        return $this;
    }
}
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

namespace Basho\Riak\Command\Search;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to fetch a search results from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * @var Command\Search\Response|null
     */
    protected $response = null;

    protected $index_name;

    protected $default_field;

    protected $default_operation;

    public function __construct(Command\Builder\Search\FetchObjects $builder)
    {
        parent::__construct($builder);

        $this->index_name = $builder->getIndexName();
    }

    public function getData()
    {
        return '';
    }

    public function getEncodedData()
    {
        return '';
    }

    public function setResponse($statusCode, $responseHeaders = [], $responseBody = '')
    {
        $this->response = new Response($statusCode, $responseHeaders, $responseBody);

        return $this;
    }

    /**
     * @return Command\Search\Response
     */
    public function execute()
    {
        return parent::execute();
    }

    public function __toString()
    {
        return $this->index_name;
    }
}
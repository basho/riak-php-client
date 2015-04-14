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

namespace Basho\Riak\Command\Search\Index;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to delete a Search Index from Riak Yokozuna
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Delete extends Command implements CommandInterface
{
    protected $method = 'DELETE';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Command\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\Search\DeleteIndex $builder)
    {
        parent::__construct($builder);

        $this->name = $builder->getName();
    }

    public function getEncodedData()
    {
        return $this->getData();
    }

    public function getData()
    {
        return '';
    }

    /**
     * @param $statusCode
     * @param array $responseHeaders
     * @param string $responseBody
     *
     * @return $this
     */
    public function setResponse($statusCode, $responseHeaders = [], $responseBody = '')
    {
        $this->response = new Command\Response($statusCode, $responseHeaders, $responseBody);

        return $this;
    }

    /**
     * @return Command\Response
     */
    public function execute()
    {
        return parent::execute();
    }

    public function __toString()
    {
        return $this->name;
    }
}
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

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to set a bucket property on a bucket
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'PUT';

    protected $properties = [];

    /**
     * @var Command\Bucket\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\SetBucketProperties $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->properties = $builder->getProperties();
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function getData()
    {
        return ['props' => $this->properties];
    }

    public function setResponse($statusCode, $responseHeaders = [], $responseBody = '')
    {
        $this->response = new Response($statusCode, $responseHeaders, $responseBody);

        return $this;
    }

    /**
     * @return Command\Bucket\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
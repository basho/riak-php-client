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

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SetBucketProperties extends Command\Builder implements Command\BuilderInterface
{
    use BucketTrait;

    /**
     * @var array
     */
    protected $properties = [];

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);

        $this->headers['Content-Type'] = self::CONTENT_TYPE_JSON;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Bucket\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\Bucket\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');

        if (count($this->properties) < 1) {
            throw new Exception('At least one element to add or remove needs to be defined.');
        }

        if (!isset($this->headers['Content-Type']) || $this->headers['Content-Type'] != self::CONTENT_TYPE_JSON) {
            throw new Exception('The \'Content-Type\' header is required to be ' . self::CONTENT_TYPE_JSON);
        }
    }
}
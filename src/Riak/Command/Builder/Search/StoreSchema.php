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

namespace Basho\Riak\Command\Builder\Search;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class StoreSchema extends Command\Builder implements Command\BuilderInterface
{
    /**
     * Name of index to create
     *
     * @var string
     */
    protected $name = '';

    protected $schema = '';

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);

        $this->headers['Content-Type'] = self::CONTENT_TYPE_XML;
    }

    public function withSchemaFile($schema_file)
    {
        $this->schema = file_get_contents($schema_file);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    public function withSchemaString($schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Schema\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Schema\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Name');
        $this->required('Schema');
    }
}
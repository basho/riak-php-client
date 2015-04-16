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

use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchSchema extends Command\Builder implements Command\BuilderInterface
{
    protected $schema_name = '';

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return $this->schema_name;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Schema\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Schema\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('SchemaName');
    }

    public function withName($name)
    {
        $this->schema_name = $name;

        return $this;
    }
}
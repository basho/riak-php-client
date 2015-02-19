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

namespace Basho\Riak\Command;

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\DataType;

/**
 * Class Builder
 *
 * This class follows the Builder design pattern and is the preferred method for creating Basho\Riak\Command
 * objects for interacting with your Riak data cluster.
 *
 * <code>
 * use Basho\Riak\Command;
 * use Basho\Riak\Bucket;
 * use Basho\Riak\Location;
 *
 * $bucket = new Bucket('users');
 *
 * $command = (new Command\Builder(Command::STORE_OBJECT))
 *      ->withObject(new Object('test_data'))
 *      ->withLocation(new Location('test_key', $bucket))
 *      ->build();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Builder
{
    /**
     * @var Riak|null
     */
    protected $riak = NULL;

    /**
     * Command parameters
     *
     * @var array
     */
    protected $parameters = [];

    public function __construct(Riak $riak)
    {
        $this->riak = $riak;
    }

    /**
     * Command build
     *
     * Validates then returns the built command object.
     */
    abstract public function build();

    public function addCounter()
    {
        $this->command->setDataType(new DataType\Counter());

        return $this;
    }

    public function addSet($data)
    {
        $this->command->setDataType(new DataType\Set($data));

        return $this;
    }

    public function addMap($data)
    {
        $this->command->setDataType(new DataType\Map($data));

        return $this;
    }

    public function addFlag()
    {
        $this->command->setDataType(new DataType\Flag());

        return $this;
    }

    public function withCounter(DataType\Counter $counter)
    {
        $this->command->setDataType($counter);

        return $this;
    }

    public function withFlag(DataType\Flag $flag)
    {
        $this->command->setDataType($flag);

        return $this;
    }

    public function withMap(DataType\Map $map)
    {
        $this->command->setDataType($map);

        return $this;
    }

    public function withSet(DataType\Set $set)
    {
        $this->command->setDataType($set);

        return $this;
    }

    public function withParameter($key, $value = true)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function withParameters($parameters = [])
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getConnection()
    {
        return $this->riak;
    }

    /**
     * Validate command
     *
     * Method validates if the builder has the parameters / objects required to successfully execute the command
     *
     * @return bool
     * @throws Builder\Exception
     */
    protected function validate()
    {
        throw new Command\Builder\Exception('Invalid builder.');
    }

    /**
     * Used to verify a property within the builder is not null and is instantiated
     *
     * @param $objectName
     *
     * @throws Builder\Exception
     */
    protected function required($objectName)
    {
        $method = "get{$objectName}";
        if (!$this->$method() && !$this->$method() instanceof $objectName) {
            throw new Builder\Exception("This command requires {$objectName} be defined.");
        }
    }
}
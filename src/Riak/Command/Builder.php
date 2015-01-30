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

use Basho\Riak\Bucket;
use Basho\Riak\Command;
use Basho\Riak\DataType;
use Basho\Riak\Location;
use Basho\Riak\Object;

/**
 * Class Builder
 *
 * This class follows the Builder design pattern and is the preferred method for creating Basho\Riak\Command
 * objects for interacting with your Riak data cluster.
 *
 * <code>
 * use Basho\Riak\Command
 *
 * $command = (new Command\Builder(Command::STORE_OBJECT))
 *      ->withObject(new Object('test_key'))
 *      ->withNamespace('/default/users')
 *      ->build();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Builder
{
    /**
     * Command object to be built
     *
     * @var Command|null
     */
    protected $command = null;

    public function __construct($command)
    {
        try {
            $this->command = new $command();
        } catch (Exception $e) {
            throw new Command\Builder\Exception('Invalid command.');
        }
    }

    /**
     * Command build
     *
     * Validates then returns the built command object.
     *
     * @return Command|null
     */
    public function build()
    {
        // validate command is ready to execute
        $this->command->validate();

        return $this->command;
    }

    /**
     * Add a newly created object
     *
     * @param string $data
     *
     * @return $this
     */
    public function addObject($data)
    {
        $object = new Object($data);
        $this->command->setObject($object);

        return $this;
    }

    public function addCounter()
    {
        $this->command->setDataType(new DataType\Counter());
    }

    public function addSet($data)
    {
        $this->command->setDataType(new DataType\Set($data));
    }

    public function addMap($data)
    {
        $this->command->setDataType(new DataType\Map($data));
    }

    public function addFlag()
    {
        $this->command->setDataType(new DataType\Flag());
    }

    /**
     * Accepts a Bucket object or a bucket string
     *
     * @param Bucket|string $bucket
     *
     * @return $this
     * @throws Builder\Exception
     */
    public function withBucket($bucket)
    {
        if ($bucket instanceof Bucket) {
            $this->command->setBucket($bucket);
        } elseif (is_string($bucket)) {
            $this->command->setBucket(new Bucket($bucket));
        } else {
            throw new Command\Builder\Exception('Invalid argument.');
        }

        return $this;
    }

    /**
     * Accepts a Location object or a location string
     *
     * @param Location|string $location
     *
     * @return $this
     * @throws Builder\Exception
     */
    public function withLocation($location)
    {
        if ($location instanceof Location) {
            $this->command->setLocation($location);
        } elseif (is_string($location) && substr($location, 0, 1) === '/') {
            // parse the locator string into $matches
            preg_match('/^\/(\w)\/(\w)\/(\w)$/', $location, $matches);

            // build and set the Location object
            $this->command->setLocation(
                new Location($matches[2], new Bucket($matches[1], $matches[0]))
            );
        } else {
            throw new Command\Builder\Exception('Invalid argument.');
        }

        return $this;
    }

    public function withObject(Object $object)
    {
        $this->command->setObject($object);

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
        $this->command->setParameter($key, $value);

        return $this;
    }

    public function withParameters($parameters = [])
    {
        $this->command->setParameters($parameters);

        return $this;
    }
}
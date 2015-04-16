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

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\Node;

$node = (new Node\Builder)
    ->atHost('riak-test')
    ->onPort(8098)
    ->build();

$riak = new Riak([$node]);

$user = new \StdClass();
$user->name = 'John Doe';
$user->email = 'jdoe@example.com';

// store a new value
$command = (new Command\Builder\StoreObject($riak))
    ->buildJsonObject($user)
    ->buildBucket('users')
    ->build();

$response = $command->execute();

$location = $response->getLocation();

$command = (new Command\Builder\FetchObject($riak))
    ->atLocation($location)
    ->build();

$response = $command->execute();

$object = $response->getObject();

$object->getData()->country = 'USA';

$command = (new Command\Builder\StoreObject($riak))
    ->withObject($object)
    ->atLocation($location)
    ->build();

$response = $command->execute();

echo $response->getStatusCode() . PHP_EOL;
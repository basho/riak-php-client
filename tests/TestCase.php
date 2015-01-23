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

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Node\Builder;
use Pimple\Container;

/**
 * Class TestCase
 *
 * Main class for testing Riak clustering
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container|null
     */
    protected static $container = null;

    public static function setUpBeforeClass()
    {
        static::$container = new Container();

        static::$container['nodes'] = function ($c) {
            return (new Builder())
                ->buildLocalhost();
        };

        static::$container['riak'] = function ($c) {
            return new Riak($c['nodes']);
        };
    }

    public static function tearDownAfterClass()
    {
        static::$container = null;
    }
}
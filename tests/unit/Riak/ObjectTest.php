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

namespace Basho\Tests\Riak;

use Basho\Riak\Object;
use Basho\Tests\TestCase;

/**
 * Class ObjectTest
 *
 * Test set for key value objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ObjectTest extends TestCase
{
    public function testConstruct()
    {
        // simple new object
        $object = new Object();
        $this->assertEmpty($object->getData());
        $this->assertNotEmpty($object->getHeaders());
        $this->assertEquals($object->getHeader('content-type'), 'application/json');

        // more complex object
        $data = new \StdClass();
        $data->woot = 'sauce';
        $object = new Object($data, ['content-type' => 'text/plain']);
        $this->assertEquals('sauce', $object->getData()->woot);
        $this->assertNotEmpty($object->getHeaders());
        $this->assertEquals($object->getHeader('content-type'), 'text/plain');
    }
}
<?php

/*
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

/**
 * Class ObjectTest
 *
 * Test set for key value objects
 *
 * @package     Basho\Tests\RiakTest
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestIncomplete();
    }

    public function testConstruct()
    {
        // simple new object
        $object = new Object();
        $this->assertEmpty($object->getKey());
        $this->assertEmpty($object->getData());
        $this->assertNotEmpty($object->getHeaders());
        $this->assertEquals($object->getHeader('content-type'), 'application/json');

        // more complex object
        $key = 'this_is_a_key';
        $data = new \StdClass();
        $data->woot = 'sauce';
        $object = new Object($key, $data, ['content-type' => 'text/plain']);
        $this->assertEquals($key, $object->getKey());
        $this->assertEquals('sauce', $object->getData()->woot);
        $this->assertNotEmpty($object->getHeaders());
        $this->assertEquals($object->getHeader('content-type'), 'text/plain');
    }

    public function testSetHeader()
    {
        $object = new Object();
        $object->setHeader('content-type', 'text/plain');
        $this->assertEquals($object->getHeader('content-type'), 'text/plain');
    }
}
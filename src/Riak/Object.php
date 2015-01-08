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

namespace Basho\Riak;

use Basho\Riak\Link;
use Basho\Riak\MapReduce;

/**
 * Abstract Class DataType
 *
 * Main class for data objects in Riak
 *
 * @package     Basho\Riak
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class Object
{
    use ObjectTrait;

    /**
     * Stored data or object
     *
     * @var mixed|null
     */
    protected $data = null;

    /**
     * @param string     $key
     * @param mixed|null $data
     * @param array|null $headers
     */
    public function __construct($key = '', $data = null, $headers = null)
    {
        $this->setKey($key);
        $this->setData($data);

        if (!empty($headers) && is_array($headers)) {
            $this->setHeaders($headers);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
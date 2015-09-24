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

namespace Basho\Riak\Command\DataType\Set;

use Basho\Riak\DataType\Set;
use Basho\Riak\Location;

/**
 * Container for a response related to an operation on a set data type
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\DataType\Set|null
     */
    protected $set = null;

    public function __construct($success = true, $code = 0, $message = '', $location = null, $set = null, $date = '')
    {
        parent::__construct($success, $code, $message);

        $this->set = $set;
        $this->location = $location;
        $this->date = $date;
    }

    /**
     * Retrieves the Location value from the response headers
     *
     * @return Location
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return Set|null
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * Retrieves the date of the counter's retrieval
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getDate()
    {
        return $this->date;
    }
}
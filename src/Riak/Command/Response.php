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

namespace Basho\Riak\Command;

/**
 * Data structure for handling Command responses from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response
{
    protected $success = false;

    protected $code = '';

    protected $message = '';

    public function __construct($success = true, $code = 0, $message = '')
    {
        $this->success = $success;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->code == '404' ? true : false;
    }

    public function isUnauthorized()
    {
        return $this->code == '401' ? true : false;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
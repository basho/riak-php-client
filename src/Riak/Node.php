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

/**
 * Class Node
 *
 * @package     Basho\Riak
 * @author      Riak Team and contributors <eng@basho.com> (https://github.com/basho/riak-php-client/contributors)
 * @copyright   2011-2014 Basho Technologies, Inc. and contributors.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 */
class Node
{
    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 8098;

    public function __construct($host = '', $port = 0)
    {
        $this->setHost($host);
        $this->setPort($port);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setHost($value)
    {
        if ($value) {
            $this->host = $value;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setPort($value)
    {
        if ($value) {
            $this->port = $value;
        }

        return $this;
    }
}
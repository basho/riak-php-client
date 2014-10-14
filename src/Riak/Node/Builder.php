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

namespace Basho\Riak\Node;

use Basho\Riak\Node;

/**
 * Class Node Builder
 *
 * @package     Basho\Riak\Node
 * @author      Riak Team and contributors <eng@basho.com> (https://github.com/basho/riak-php-client/contributors)
 * @copyright   2011-2014 Basho Technologies, Inc. and contributors.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 */
class Builder extends Node
{
    public function build()
    {
        return new Node($this->host, $this->port);
    }

    public function buildCluster(array $hosts = ['127.0.0.1'])
    {
        foreach ($hosts as $host) {
            return new Node($host, $this->port);
        }
    }

    public function buildLocalhost(array $ports = [8098])
    {
        foreach ($ports as $port) {
            return new Node('127.0.0.1', $port);
        }
    }
}
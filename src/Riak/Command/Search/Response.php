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

namespace Basho\Riak\Command\Search;

use Basho\Riak\Search\Doc;

/**
 * Container for a response for receiving data back from a Search request on Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    protected $results = '';

    protected $docs = [];

    public function __construct($statusCode, $headers = [], $body = '')
    {
        parent::__construct($statusCode, $headers, $body);

        // make sure body is not only whitespace
        if (trim($body)) {
            $this->results = json_decode($body);
            foreach ($this->results->response->docs as $doc) {
                $this->docs[] = new Doc($doc);
            }
        }
    }

    public function getNumFound()
    {
        return $this->results->response->numFound;
    }

    public function getDocs()
    {
        return $this->docs;
    }
}
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

use Basho\Riak\Command;
use Basho\Riak\Location;

/**
 * Base class for Commands performing operations on Kv Objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Object extends Command
{
    /**
     * @var Object\Response|null
     */
    protected $response = NULL;

    /**
     * @var \Basho\Riak\Object|null
     */
    protected $object = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    protected $decodeAsAssociative = false;

    public function getObject()
    {
        return $this->object;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getEncodedData()
    {
        $data = $this->getData();

        if (in_array($this->object->getContentType(), ['application/json', 'text/json'])) {
            return json_encode($data);
        }

        return rawurlencode($data);
    }

    public function getDecodedData($data, $contentType)
    {
        return static::decodeData($data, $contentType, $this->decodeAsAssociative);
    }

    public static function decodeData($data, $contentType = '', $decodeAsAssociative = false)
    {
        if (in_array($contentType, ['application/json', 'text/json'])) {
            return json_decode($data, $decodeAsAssociative);
        }

        return rawurlencode($data);
    }

    public function getData()
    {
        return $this->object->getData();
    }

    /**
     * @return Command\Object\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
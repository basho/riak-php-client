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

namespace Basho\Riak\Search;

use Basho\Riak\Bucket;
use Basho\Riak\Location;

/**
 * Data structure for document objects returned from Solr
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Doc
{
    protected $data = null;

    protected $_yz_id = '';
    protected $_yz_rk = '';
    protected $_yz_rt = '';
    protected $_yz_rb = '';

    public function __construct(\stdClass $data)
    {
        if (isset($data->_yz_id)) {
            $this->_yz_id = $data->_yz_id;
            unset($data->_yz_id);
        }

        if (isset($data->_yz_rk)) {
            $this->_yz_rk = $data->_yz_rk;
            unset($data->_yz_rk);
        }

        if (isset($data->_yz_rt)) {
            $this->_yz_rt = $data->_yz_rt;
            unset($data->_yz_rt);
        }

        if (isset($data->_yz_rb)) {
            $this->_yz_rb = $data->_yz_rb;
            unset($data->_yz_rb);
        }

        $this->data = $data;
    }

    public function getLocation()
    {
        return new Location($this->_yz_rk, new Bucket($this->_yz_rb, $this->_yz_rt));
    }

    public function __get($name)
    {
        return $this->data->{$name};
    }
}
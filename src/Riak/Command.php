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
 * Class Command
 *
 * The command class is used to build a read or write command to be executed against a Riak node.
 *
 * @package     Basho\Riak
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
abstract class Command
{
    /**
     * Request method
     *
     * This can be GET, POST, PUT, or DELETE
     *
     * @see http://docs.basho.com/riak/latest/dev/references/http/
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Riak Bucket
     *
     * @var Bucket|null
     */
    protected $bucket = null;

    /**
     * Riak Object
     *
     * @var \Basho\Riak\Object|null
     */
    protected $object = null;

    /**
     * Command parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Command has parameters?
     *
     * @return bool
     */
    public function hasParameters()
    {
        return (bool)count($this->parameters);
    }

    /**
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param Bucket|null $bucket
     * @return $this
     */
    public function setBucket(Bucket $bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * @return \Basho\Riak\Object|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param \Basho\Riak\Object|null $object
     * @return $this
     */
    public function setObject(Object $object)
    {
        $this->object = $object;

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
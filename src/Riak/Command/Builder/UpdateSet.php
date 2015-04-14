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

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class UpdateSet extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * @var array
     */
    protected $add_all = [];

    /**
     * @var array
     */
    protected $remove_all = [];

    /**
     * Similar to Vector Clocks, the context allows us to determine the state of a CRDT Set
     *
     * @var string
     */
    protected $context = '';

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);

        $this->headers['Content-Type'] = self::CONTENT_TYPE_JSON;
    }

    /**
     * @param mixed $element
     *
     * @return $this
     */
    public function add($element)
    {
        settype($element, 'string');
        $this->add_all[] = $element;

        return $this;
    }

    /**
     * @param mixed $element
     *
     * @return $this
     */
    public function remove($element)
    {
        settype($element, 'string');
        $this->remove_all[] = $element;

        return $this;
    }

    /**
     * @param $context
     *
     * @return $this
     */
    public function withContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return array
     */
    public function getAddAll()
    {
        return $this->add_all;
    }

    /**
     * @return array
     */
    public function getRemoveAll()
    {
        return $this->remove_all;
    }

    /**
     * getContext
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\Set\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Set\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');

        $count_add = count($this->add_all);
        $count_remove = count($this->remove_all);

        if ($count_add + $count_remove < 1) {
            throw new Exception('At least one element to add or remove needs to be defined.');
        }

        // if we are performing a remove, Location and context are required
        if ($count_remove) {
            $this->required('Location');
            $this->required('Context');
        }

        if (!isset($this->headers['Content-Type']) || $this->headers['Content-Type'] != self::CONTENT_TYPE_JSON) {
            throw new Exception('The \'Content-Type\' header is required to be ' . self::CONTENT_TYPE_JSON);
        }
    }
}
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

namespace Basho\Riak\Command\Object;

use Basho\Riak\Location;
use Basho\Riak\Object;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\Object|null
     */
    protected $object = NULL;

    /**
     * @var \Basho\Riak\Object[]
     */
    protected $siblings = [];

    /**
     * @var bool
     */
    private $decodeAsAssociative = false;

    public function __construct($statusCode, $headers = [], $body = '', $decodeAsAssociative = false)
    {
        parent::__construct($statusCode, $headers, $body);

        $this->decodeAsAssociative = $decodeAsAssociative;

        // make sure body is not only whitespace
        if (trim($body) && !$this->hasSiblings()) {
            $this->parseObject();
        } elseif (trim($body) && $this->hasSiblings()) {
            $this->parseSiblings();
        }
    }

    /**
     * @return bool
     */
    public function hasSiblings()
    {
        return $this->statusCode == '300' ? true : false;
    }

    private function parseObject()
    {
        $headers = $this->headers;
        if (in_array($headers['Content-Type'], ['application/json', 'text/json'])) {
            $data = json_decode($this->body, $this->decodeAsAssociative);

        } else {
            $data = rawurldecode($this->body);
        }

        // if the following headers exist, remove them
        if (isset($headers['Content-Length'])) {
            unset($headers['Content-Length']);
        }
        if (isset($headers['Server'])) {
            unset($headers['Server']);
        }

        $this->object = new Object($data, $headers);
    }

    private function parseSiblings()
    {
        $position = strpos($this->headers['Content-Type'], 'boundary=');
        $parts = explode('--' . substr($this->headers['Content-Type'], $position + 9), $this->body);
        foreach ($parts as $part) {
            $headers = [];
            $slice_point = 0;
            $empties = 0;

            $lines = preg_split('/\n\r|\n|\r/', trim($part));
            foreach ($lines as $key => $line) {
                if (strpos($line, ':')) {
                    $empties = 0;
                    list ($key, $value) = explode(':', $line);

                    $value = trim($value);

                    if (!empty($value)) {
                        if (!isset($headers[$key])) {
                            $headers[$key] = $value;
                        } elseif (is_array($headers[$key])) {
                            $headers[$key] = array_merge($headers[$key], [$value]);
                        } else {
                            $headers[$key] = array_merge([$headers[$key]], [$value]);
                        }
                    }
                } elseif ($line == '') {
                    // if we have two line breaks in a row, then we have finished headers
                    if ($empties) {
                        $slice_point = $key + 1;
                        break;
                    } else {
                        $empties++;
                    }
                }
            }

            $data = implode(PHP_EOL, array_slice($lines, $slice_point));
            $this->siblings[] = new Object($data, $headers);
        }
    }

    /**
     * Retrieves the Vclock value from the response headers
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getVclock()
    {
        return $this->getHeader('X-Riak-Vclock');
    }

    /**
     * Retrieves the Location value from the response headers
     *
     * @return Location
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLocation()
    {
        return Location::fromString($this->getHeader('Location'));
    }

    /**
     * @return \Basho\Riak\Object|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->statusCode == '404' ? true : false;
    }

    /**
     * Fetches the sibling tags from the response
     *
     * @return array
     */
    public function getSiblings()
    {
        return $this->siblings;
    }

    /**
     * Retrieves the last modified time of the object
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLastModified()
    {
        return $this->getHeader('Last-Modified');
    }

    /**
     * Retrieves the etag of the object
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getETag()
    {
        return $this->getHeader('ETag');
    }

    /**
     * Retrieves the date of the object's retrieval
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getDate()
    {
        return $this->getHeader('Date');
    }
}
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

namespace Basho\Riak\MapReduce;

/**
 * MapReducePhase
 *
 * @category   Basho
 * @author     debo <marco.debo.debortoli@gmail.com> (https://github.com/MarcoDeBortoli)
 */
class Phase
{
    /**
     * Construct a Phase object.
     *
     * @param string  $type     - "map" or "reduce"
     * @param mixed   $function - string or array()
     * @param string  $language - "javascript" or "erlang"
     * @param boolean $keep     - True to return the output of this phase in
     *                          the results.
     * @param mixed   $arg      - Additional value to pass into the map or
     *                          reduce function.
     */
    public function __construct($type, $function, $language, $keep, $arg)
    {
        $this->type     = $type;
        $this->language = $language;
        $this->function = $function;
        $this->keep     = $keep;
        $this->arg      = $arg;
    }

    /**
     * Convert the Phase to an associative array. Used
     * internally.
     */
    public function to_array()
    {
        $stepdef = [
            "keep"     => $this->keep,
            "language" => $this->language,
            "arg"      => $this->arg
        ];

        if ($this->language == "javascript" && is_array($this->function)) {
            $stepdef["bucket"] = $this->function[0];
            $stepdef["key"]    = $this->function[1];
        } else {
            if ($this->language == "javascript" && is_string($this->function)) {
                if (strpos($this->function, "{") == false) {
                    $stepdef["name"] = $this->function;
                } else {
                    $stepdef["source"] = $this->function;
                }
            } else {
                if ($this->language == "erlang" && is_array($this->function)) {
                    $stepdef["module"]   = $this->function[0];
                    $stepdef["function"] = $this->function[1];
                }
            }
        }

        return [($this->type) => $stepdef];
    }
}
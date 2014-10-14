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

namespace Basho\Riak\Link;

/**
 * Phase
 *
 * @category   Basho
 * @author     debo <marco.debo.debortoli@gmail.com> (https://github.com/MarcoDeBortoli)
 */
class Phase
{
    /**
     * Construct a Phase object.
     *
     * @param string  $bucket - The bucket name.
     * @param string  $tag    - The tag.
     * @param boolean $keep   - True to return results of this phase.
     */
    public function __construct($bucket, $tag, $keep)
    {
        $this->bucket = $bucket;
        $this->tag = $tag;
        $this->keep = $keep;
    }

    /**
     * Convert the Phase to an associative array. Used
     * internally.
     */
    public function to_array()
    {
        $stepdef = [
            "bucket" => $this->bucket,
            "tag"  => $this->tag,
            "keep" => $this->keep
        ];

        return ["link" => $stepdef];
    }
}
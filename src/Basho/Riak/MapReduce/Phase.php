<?php
/**
 * Riak PHP Client
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Apache License, Version 2.0 that is
 * bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to <eng@basho.com> so we can send you a copy immediately.
 *
 * @category   Basho
 * @copyright  Copyright (c) 2013 Basho Technologies, Inc. and contributors.
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
     * @param string $type - "map" or "reduce"
     * @param mixed $function - string or array()
     * @param string $language - "javascript" or "erlang"
     * @param boolean $keep - True to return the output of this phase in
     * the results.
     * @param mixed $arg - Additional value to pass into the map or
     * reduce function.
     */
    public function __construct($type, $function, $language, $keep, $arg)
    {
        $this->type = $type;
        $this->language = $language;
        $this->function = $function;
        $this->keep = $keep;
        $this->arg = $arg;
    }

    /**
     * Convert the Phase to an associative array. Used
     * internally.
     */
    public function to_array()
    {
        $stepdef = array(
            "keep" => $this->keep,
            "language" => $this->language,
            "arg" => $this->arg
        );

        if ($this->language == "javascript" && is_array($this->function)) {
            $stepdef["bucket"] = $this->function[0];
            $stepdef["key"] = $this->function[1];
        } else {
            if ($this->language == "javascript" && is_string($this->function)) {
                if (strpos($this->function, "{") == false) {
                    $stepdef["name"] = $this->function;
                } else {
                    $stepdef["source"] = $this->function;
                }
            } else {
                if ($this->language == "erlang" && is_array($this->function)) {
                    $stepdef["module"] = $this->function[0];
                    $stepdef["function"] = $this->function[1];
                }
            }
        }

        return array(($this->type) => $stepdef);
    }
}
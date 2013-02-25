<?php
/**
 * This file is part of the riak-php-client.
 *
 * PHP version 5.3+
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link https://github.com/localgod/riak-php-client
 */
namespace Basho\Riak;

/**
 * The MapReducePhase holds information about a Map phase or
 * Reduce phase in a MapReduce operation.
 * 
 * @method array to_array()
 */
class MapReducePhase
{
    /** 
     * Type of operation ("map" or "reduce")
     * @var string
     */
    private $type;

    /** 
     * Language to use ("javascript" or "erlang")
     * @var string
     */
    private $language;

    /**
     * Function to use 
     * @var string|array
     */
    private $function;

    /**
     * Should we return the output of this phase in the results. 
     * @var boolean
     */
    private $keep;

    /**
     * Additional value to pass into the map or reduce function.
     * @var mixed
     */
    private $arg;

    /**
     * Construct a MapReducePhase object.
     * 
     * @param string  $type     "map" or "reduce"
     * @param mixed   $function string or array()
     * @param string  $language "javascript" or "erlang"
     * @param boolean $keep     True to return the output of this phase in
     *                          the results.
     * @param mixed   $arg      Additional value to pass into the map or
     *                          reduce function.
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
     * Return results of current phase?
     *
     * @return boolean
     */
    public function getKeep()
    {
        return $this->keep;
    }
    
    /**
     * Return results of current phase?
     *
     * @param boolean $keep The keep value
     *
     * @return \Basho\Riak\LinkPhase
     */
    public function setKeep($keep)
    {
        $this->keep = $keep;
        return $this;
    }

    /**
     * This method is only here to maintain backwards compatibility
     * with old method names pre PSR coding standard
     *
     * @param string $name      Name of old method
     * @param array  $arguments Arguments for method
     *
     * @return void
     */
    public function __call($name, $arguments)
    {
        if ($name == 'to_array') {
            self::toArray();
        }
    }
    
    /**
     * Convert the MapReducePhase to an associative array. Used
     * internally.
     *
     * @return array
     */
    public function toArray()
    {
        $stepdef = array("keep" => $this->keep, "language" => $this->language,
                "arg" => $this->arg);

        if ($this->language == "javascript" && is_array($this->function)) {
            $stepdef["bucket"] = $this->function[0];
            $stepdef["key"] = $this->function[1];
        } elseif ($this->language == "javascript"
                && is_string($this->function)) {
            if (strpos($this->function, "{") == false) {
                $stepdef["name"] = $this->function;
            } else {
                $stepdef["source"] = $this->function;
            }
        } elseif ($this->language == "erlang" && is_array($this->function)) {
            $stepdef["module"] = $this->function[0];
            $stepdef["function"] = $this->function[1];
        }

        return array(($this->type) => $stepdef);
    }
}

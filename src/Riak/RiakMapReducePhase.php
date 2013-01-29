<?php
/**
 * The RiakMapReducePhase holds information about a Map phase or
 * Reduce phase in a RiakMapReduce operation.
 * @package RiakMapReducePhase
 */
class RiakMapReducePhase
{
    /**
     * Construct a RiakMapReducePhase object.
     * @param string  $type     "map" or "reduce"
     * @param mixed   $function string or array()
     * @param string  $language "javascript" or "erlang"
     * @param boolean $keep     True to return the output of this phase in 
     *                          the results.
     * @param mixed   $arg      Additional value to pass into the map or 
     *                          reduce function.
     *                          
     * @return void
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
     * This method is only here to maintain backwards compatibility
     * with old method names pre PSR codingstandard
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
     * Convert the RiakMapReducePhase to an associative array. Used
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

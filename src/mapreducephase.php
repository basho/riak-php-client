<?php 
namespace Riak;

/**
 * The Riak\MapReducePhase holds information about a Map phase or
 * Reduce phase in a Riak\MapReduce operation.
 * @package Riak\MapReducePhase
 */
class MapReducePhase {
  /**
   * Construct a Riak\MapReducePhase object.
   * @param string $type - "map" or "reduce"
   * @param mixed $function - string or array() 
   * @param string $language - "javascript" or "erlang"
   * @param boolean $keep - True to return the output of this phase in
   * the results.
   * @param mixed $arg - Additional value to pass into the map or
   * reduce function.
   */
  function __construct($type, $function, $language, $keep, $arg) {
    $this->type = $type;
    $this->language = $language;
    $this->function = $function;
    $this->keep = $keep;
    $this->arg = $arg;
  }
  
  /**
   * Convert the Riak\MapReducePhase to an associative array. Used
   * internally.
   */
  function to_array() {
    $stepdef = array("keep"=>$this->keep,
                     "language"=>$this->language,
                     "arg"=>$this->arg);

    if ($this->language == "javascript" && is_array($this->function)) {
      $stepdef["bucket"] = $this->function[0];      
      $stepdef["key"] = $this->function[1];      
    } else if ($this->language == "javascript" && is_string($this->function)) {
      if (strpos($this->function, "{") == FALSE) 
        $stepdef["name"] = $this->function;      
      else
        $stepdef["source"] = $this->function;      
    } else if ($this->language == "erlang" && is_array($this->function)) {
      $stepdef["module"] = $this->function[0];
      $stepdef["function"] = $this->function[1];
    }
    
    return array(($this->type)=>$stepdef);
  }
}

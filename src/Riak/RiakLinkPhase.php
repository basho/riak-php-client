<?php
/**
 * The RiakLinkPhase object holds information about a Link phase in a
 * map/reduce operation.
 * 
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class RiakLinkPhase
{
	/** @var string */
	private $bucket;
	
	/** @var string|null */
	private $tag;

	/** @var boolean */
	private $keep;
	
    /**
     * Construct a RiakLinkPhase object.
     * 
     * @param string  $bucket The bucket name.
     * @param string  $tag    The tag.
     * @param boolean $keep   True to return results of this phase.
     * 
     * @return void
     */
    public function __construct($bucket, $tag, $keep)
    {
        $this->bucket = $bucket;
        $this->tag = $tag;
        $this->keep = $keep;
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
     * Convert the RiakLinkPhase to an associative array. Used
     * internally.
     * 
     * @return array
     */
    public function toArray()
    {
        $stepdef = array("bucket" => $this->bucket, "tag" => $this->tag,
                "keep" => $this->keep);

        return array("link" => $stepdef);
    }
}

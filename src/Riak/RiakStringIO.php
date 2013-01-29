<?php
/**
 * Private class used to accumulate a CURL response.
 * @package RiakStringIO
 */
class RiakStringIO
{
	/**
	 * Create new Riak string
	 * 
	 * @return void
	 */
    public function __construct()
    {
        $this->contents = '';
    }

    /**
     * Write
     * 
     * @param unknown $ch
     * @param string  $data Data to write
     * 
     * @return integer
     */
    public function write($ch, $data)
    {
        $this->contents .= $data;

        return strlen($data);
    }

    /**
     * Get content
     * 
     * @return string
     */
    public function contents()
    {
        return $this->contents;
    }
}

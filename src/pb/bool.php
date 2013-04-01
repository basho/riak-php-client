<?php
namespace Riak\PB;

/**
 * @author Nikolai Kordulla
 */
class Bool extends Int
{
	var $wired_type = Message::WIRED_VARINT;

	/**
	 * Parses the message for this type
	 *
	 * @param array
	 */
	public function ParseFromArray()
	{
		$this->value = $this->reader->next();
		$this->value = ($this->value != 0) ? TRUE : FALSE;
		
		$this->clean();
	}
	
	public function value(){
	    return $this->value ? TRUE : FALSE;
	}

}

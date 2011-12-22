<?php
namespace Riak\PB;

/**
 * @author Nikolai Kordulla
 */
class Scalar extends Message
{

	protected function clean()
	{
		unset($this->reader);		
		unset($this->values);
		unset($this->chunk);
		unset($this->_d_string);
	}

	public function __toString(){
	    return self::toString( '', $this->value );
	}
}

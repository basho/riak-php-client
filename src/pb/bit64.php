<?php
namespace Riak\PB;

/**
 * @author Nikolai Kordulla
 */
class Bit64 extends Scalar
{
	var $wired_type = Message::WIRED_64BIT;

	/**
	 * Parses the message for this type
	 *
	 * @param array
	 */
	public function ParseFromArray()
	{					
		$pointer = $this->reader->get_pointer();
		$this->reader->add_pointer(8);
		$str = $this->reader->get_message_from($pointer);
		
		$p = unpack("d", $str);
		$this->value = $p[1];  		
		
		$this->clean();
	}

	/**
	 * Serializes type
	 */
	public function SerializeToString($rec=-1)
	{
		// first byte is length byte
		$string = '';

		if ($rec > -1)
		{
			$string .= $this->base128->set_value($rec << 3 | $this->wired_type);
		}

		//$value = $this->base128->set_value($this->value);
		$value = pack("d", $this->value);
		$string .= $value;

		return $string;
	}
}


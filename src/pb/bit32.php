<?php
namespace Riak\PB;

/**
 * @author Nikolai Kordulla
 */
class Bit32 extends Scalar
{
	protected $wired_type = Message::WIRED_32BIT;

	/**
	 * Parses the message for this type
	 *
	 * @param array
	 */
	public function ParseFromArray()
	{					
		$pointer = $this->reader->get_pointer();
		$this->reader->add_pointer(4);
		$str = $this->reader->get_message_from($pointer);
		
		$p = unpack("f", $str);
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
		$value = pack("f", $this->value);
		$string .= $value;

		return $string;
	}
}

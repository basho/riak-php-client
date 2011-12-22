<?php
namespace Riak\PB;

/**
 * @author Nikolai Kordulla
 */
class String extends Scalar
{
	var $wired_type = Message::WIRED_LENGTH_DELIMITED;

	/**
	 * Parses the message for this type
	 *
	 * @param array
	 */
	public function ParseFromArray()
	{
		$this->value = '';
		// first byte is length
		$length = $this->reader->next();

		// just extract the string
		$pointer = $this->reader->get_pointer();
		$this->reader->add_pointer($length);
		$this->value = $this->reader->get_message_from($pointer);
		
		$this->clean();
	}
		
	/**
	 * Serializes type
	 */
	public function SerializeToString($rec = -1)
	{
		$string = '';

		if ($rec > -1)
		{
			$string .= $this->base128->set_value($rec << 3 | $this->wired_type);
		}

		$string .= $this->base128->set_value(strlen($this->value));
		$string .= $this->value;

		return $string;
	}
	
	public function toJson($fieldName = "")
	{
		$search  = array('\\', "\n", "\t", "\r", "\b", "\f", '"', "/");
        $replace = array('\\\\', '\\n', '\\t', '\\r', '\\b', '\\f', '\"', "\/");
        $value  = str_replace($search, $replace, $this->value);
        
        // Escape certain ASCII characters:
        // 0x08 => \b
        // 0x0c => \f
        $value = str_replace(array(chr(0x08), chr(0x0C)), array('\b', '\f'), $value);
        $value = self::encodeUnicodeString($value);
        
        if ($fieldName == "")
			return  '"'. $value . '"';
		else		
			return  '"' . $fieldName . '": "' . $value . '"';		
	}
	
 /**
     * Encode Unicode Characters to \u0000 ASCII syntax.
     *
     * This algorithm was originally developed for the
     * Solar Framework by Paul M. Jones
     *
     * @link   http://solarphp.com/
     * @link   http://svn.solarphp.com/core/trunk/Solar/Json.php
     * @param  string $value
     * @return string
     */
    public static function encodeUnicodeString($value)
    {
        $strlen_var = strlen($value);
        $ascii = "";

        /**
         * Iterate over every character in the string,
         * escaping with a slash or encoding to UTF-8 where necessary
         */
        for($i = 0; $i < $strlen_var; $i++) {
            $ord_var_c = ord($value[$i]);

            switch (true) {
                case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                    // characters U-00000000 - U-0000007F (same as ASCII)
                    $ascii .= $value[$i];
                    break;

                case (($ord_var_c & 0xE0) == 0xC0):
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c, ord($value[$i + 1]));
                    $i += 1;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xF0) == 0xE0):
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]));
                    $i += 2;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xF8) == 0xF0):
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]),
                                 ord($value[$i + 3]));
                    $i += 3;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xFC) == 0xF8):
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]),
                                 ord($value[$i + 3]),
                                 ord($value[$i + 4]));
                    $i += 4;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xFE) == 0xFC):
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]),
                                 ord($value[$i + 3]),
                                 ord($value[$i + 4]),
                                 ord($value[$i + 5]));
                    $i += 5;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
            }
        }

        return $ascii;
     }
     
	protected static function _utf82utf16($utf8)
    {
        // Check for mb extension otherwise do by hand.
        if( function_exists('mb_convert_encoding') ) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch (strlen($utf8)) {
            case 1:
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return $utf8;

            case 2:
                // return a UTF-16 character from a 2-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x07 & (ord($utf8{0}) >> 2))
                     . chr((0xC0 & (ord($utf8{0}) << 6))
                         | (0x3F & ord($utf8{1})));

            case 3:
                // return a UTF-16 character from a 3-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr((0xF0 & (ord($utf8{0}) << 4))
                         | (0x0F & (ord($utf8{1}) >> 2)))
                     . chr((0xC0 & (ord($utf8{1}) << 6))
                         | (0x7F & ord($utf8{2})));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }
}

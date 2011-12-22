<?php
namespace Riak\PB;

/**
 * Abstract Message class
 * @author Nikolai Kordulla
 */
abstract class Message
{
    const WIRED_VARINT = 0;
    const WIRED_64BIT = 1;
    const WIRED_LENGTH_DELIMITED = 2;
    const WIRED_START_GROUP = 3;
    const WIRED_END_GROUP = 4;
    const WIRED_32BIT = 5;       

    protected $base128;

    // here are the field types
    protected static $fields = array();
    protected static $fieldNames = array();
    
    // the values for the fields
    protected  $values = array();
    
    protected $value = NULL;

    // type of the class
    protected $wired_type = 2;

    // modus byte or string parse (byte for productive string for better reading and debuging)
    // 1 = byte, 2 = String
    const MODUS = 1;

    // now use pointer for speed improvement
    // pointer to begin
    protected $reader;

    // chunk which the class not understands
    protected $chunk = '';

    // variable for Send method
    protected $_d_string = '';      

    /**
     * Constructor - initialize base128 class
     */
    public function __construct($reader=null)
    {
        $this->reader = $reader;
        $this->base128 = new base128varint(self::MODUS);
    }

    /**
     * Get the wired_type and field_type
     * @param $number as decimal
     * @return array wired_type, field_type
     */
    public function get_types($number)
    {
        $binstring = decbin($number);
        $types = array();
        $low = substr($binstring, strlen($binstring) - 3, strlen($binstring));
        $high = substr($binstring,0, strlen($binstring) - 3) . '0000';
        $types['wired'] = bindec($low);
        $types['field'] = bindec($binstring) >> 3;
        return $types;
    }
    
    
	protected function clean()
	{
		unset($this->reader);				
		unset($this->chunk);
		unset($this->_d_string);
	}
    
    /**
     * Encodes a Message
     * @return string the encoded message
     */
    public function SerializeToString($rec=-1)
    {
        $string = '';
        // wired and type
        if ($rec > -1)
        {
            $string .= $this->base128->set_value($rec << 3 | $this->wired_type);
        }

        $stringinner = '';

        foreach (static::$fields as $index => $field)
        {
            if (is_array($this->values[$index]) && count($this->values[$index]) > 0)
            {
                // make serialization for every array
                foreach ($this->values[$index] as $array)
                {
                    $newstring = '';
                    $newstring .= $array->SerializeToString($index);

                    $stringinner .= $newstring;
                }
            }
            else if ($this->values[$index] != null)
            {
                // wired and type
                $newstring = '';
                $newstring .= $this->values[$index]->SerializeToString($index);

                $stringinner .= $newstring;
            }
        }

        /// $this->_serialize_chunk($stringinner);

        if ($this->wired_type == self::WIRED_LENGTH_DELIMITED && $rec > -1)
        {
            $stringinner = $this->base128->set_value(strlen($stringinner) / self::MODUS) . $stringinner;
        }

        return $string . $stringinner;
    }

    /**
     * Serializes the chunk
     * @param String $stringinner - String where to append the chunk
     */
    public function _serialize_chunk(&$stringinner)
    {
        $stringinner .= $this->chunk;
    }

    /**
     * Decodes a Message and Built its things
     *
     * @param message as stream of hex example '1a 03 08 96 01'
     */
    public function ParseFromString($message)
    {
        $this->reader = new InputStringReader($message);
        $this->_ParseFromArray();
        
        $this->clean();        
        
    }

    /**
     * Internal function
     */
    public function ParseFromArray()
    {
        $this->chunk = '';
        // read the length byte
        $length = $this->reader->next();
        // just take the splice from this array
        $this->_ParseFromArray($length);
        
        $this->clean();
    }

    /**
     * Internal function
     */
    private function _ParseFromArray($length=99999999)
    {
    	
        $_begin = $this->reader->get_pointer();
        while ($this->reader->get_pointer() - $_begin < $length)
        {
            $next = $this->reader->next();
            
            if ($next === false)
                break;

            // now get the message type
            $messtypes = $this->get_types($next);

            // now make method test
            if (!isset(static::$fields[$messtypes['field']]))
            {
                // field is unknown so just ignore it
                // throw new Exception('Field ' . $messtypes['field'] . ' not present ');
                if ($messtypes['wired'] == self::WIRED_LENGTH_DELIMITED)
                {
                    $consume = new String($this->reader);
                }
                else if ($messtypes['wired'] == self::WIRED_VARINT)
                {
                    $consume = new Int($this->reader);
                }
                else
                {
                    throw new Exception('I dont understand this wired code:' . $messtypes['wired']);
                }

                // perhaps send a warning out
                // @TODO SEND CHUNK WARNING
                $_oldpointer = $this->reader->get_pointer();
                $consume->ParseFromArray();
                // now add array from _oldpointer to pointer to the chunk array
                $this->chunk .= $this->reader->get_message_from($_oldpointer);
                continue;
            }

            // now array or not
            if (is_array($this->values[$messtypes['field']]))
            {
                $_class = __NAMESPACE__ . '\\' . static::$fields[$messtypes['field']];
                $this->values[$messtypes['field']][] = new $_class($this->reader);
                $index = count($this->values[$messtypes['field']]) - 1;
                if ($messtypes['wired'] != $this->values[$messtypes['field']][$index]->wired_type)
                {
                    throw new Exception('Expected type:' . $messtypes['wired'] . ' but had ' . static::$fields[$messtypes['field']]->wired_type);
                }
                $this->values[$messtypes['field']][$index]->ParseFromArray();
            }
            else
            {
                $_class = __NAMESPACE__ . '\\' . static::$fields[$messtypes['field']];
                $this->values[$messtypes['field']] = new $_class($this->reader);
                if ($messtypes['wired'] != $this->values[$messtypes['field']]->wired_type)
                {
                    throw new Exception('Expected type:' . $messtypes['wired'] . ' but had ' . static::$fields[$messtypes['field']]->wired_type);
                }
                $this->values[$messtypes['field']]->ParseFromArray();
            }
        }
    }
    
    public function value(){
        if( isset( $this->value ) ) return $this->value;
        return $this;
    }
    
    public function __get( $key ){
        
        $index = $this->_fieldIndex( $key ); 
        
        if ($this->values[$index] === null){
            return null;
        } elseif (is_array($this->values[$index])){
        	$result = array();
        	foreach( $this->values[$index] as $k => $v ){
        	    $result[ $k ] = $v->value();
        	}
        	return $result;
        } else {
        	return $this->values[$index]->value();
        }
    }
    
    public function set_value( $v ){
        return $this->value = $v;
    }
    
    protected function encodeAsObject( $index, $value ){
        if ( $value instanceof Message ) return $value;
        if( is_array( $value ) )  {
            foreach( $value as $k => $v ) $value[ $k ] = $this->encodeAsObject( $index, $v );
            return $value;
        }
        $_class = __NAMESPACE__ . '\\' . static::$fields[$index];
        $v = new $_class();
        $v->set_value( $value );
        return $v;
    }
    
    public function add( $key, $value ){
        $index = $this->_fieldIndex( $key );
        if( ! is_array( $this->values[ $index ] ) ){
            trigger_error('invalid add: ' . $key . ' is not an array', E_USER_WARNING);
        }
        $v = $this->encodeAsObject( $index, $value );
        $this->values[ $index ][] = $v;
        return $value;
    }
    
    public function __set( $key, $value ){
        $index = $this->_fieldIndex( $key );
        $v = $this->encodeAsObject( $index, $value );
        
        if( is_array( $this->values[ $index ] ) && ! is_array( $value ) ){
            trigger_error('invalid type passed in for ' . $key, E_USER_WARNING);
        }
        $this->values[ $index ] = $v;
        return $value;
    }
    
    public function __isset( $key ){
        return $this->_fieldIndex( $key ) !== FALSE;
    }
    
    public function __unset( $key ){
        $index = $this->_fieldIndex( $key );
        if( ! isset( $this->values[ $index ] ) ) return;
        if( is_array( $this->values[ $index ] ) ) {
            $this->values[ $index ] = array();
        } else {
            $this->values[ $index ] = NULL;
        }
    }
    
    protected function _fieldIndex( $key ){
        $search = array_keys( static::$fieldNames, $key, TRUE );
        return array_pop( $search );
    }
    
    	/**
	 * Used to aid in pretty printing of Protobuf objects
	 */
	private static $print_depth = 0;
	private static $indent_char = "\t";
	private static $print_limit = 50;

	public static function toString($key, $value) {
		if (is_null($value))
			return;
		if( $key ) $key .= '=>';
		$ret = str_repeat(self::$indent_char, self::$print_depth) . "$key";
		if (is_array($value)) {
			$ret .= "array(\n";
			self::$print_depth++;
			foreach($value as $i => $v)
				$ret .= self::toString("[$i]", $v);
			self::$print_depth--;
			$ret .= str_repeat(self::$indent_char, self::$print_depth) . ")\n";
		} else {
			if (is_object($value)) {
				self::$print_depth++;
				$ret .="\n";
				$ret .= $value->__toString() . "\n";
				self::$print_depth--;
				$ret .= str_repeat(self::$indent_char, self::$print_depth) . "\n";
			} elseif (is_string($value)) {
				$safevalue = addcslashes($value, "\0..\37\177..\377");
				if (strlen($safevalue) > self::$print_limit) {
					$safevalue = substr($safevalue, 0, self::$print_limit) . '...';
				}

				$ret .= '"' . $safevalue . '" (' . strlen($value) . " bytes)\n";
				
			} elseif (is_bool($value)) {
				$ret .= ($value ? 'true' : 'false') . "\n";
			} else {
				$ret .= (string)$value . "\n";
			}
		}
		return $ret;
	}
    
    public function __toString(){
        $data = array();
        foreach( static::$fieldNames as $i => $name ){
            $value = $this->values[ $i ];
            if( ! $value ) continue;
            $data[ $name ] = $value;
        }
        return self::toString( get_class( $this ), $data );
        
    }
}

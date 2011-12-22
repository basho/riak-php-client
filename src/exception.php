<?php
namespace Riak;

/**
 * A basic exception class that allows us to attach arbitrary debug to it like query result objects.
 * Nothing too complicated for now, but maybe we subclass it later to be able to distinguish 
 * different types of errors.
 */
class Exception extends \Exception {

   /**
    * @mixed    arbitrary debug info attached by the constructor.
    */
	protected $debug;
	
	const DEBUG_HEADER = "\nDebug: \n# ";
	
   /**
	* Pass in a message and optional debug. can be an object, array, or scalar value.
	* We don't assign a code at all, since it isn't that useful
	*/
	public function __construct($msg, $debug = NULL){
	    // default behavior, call the parent, no code.
        parent::__construct($msg);
        
        // attach debug, whatever it is.
        $this->debug = $debug;
    }
    
   /**
    * Return the debug just as it was passed in.
    */
    public function getDebug(){ 
        return $this->debug; 
    }
    
   /**
    * convert the exception into a string representation.
    * if the debug is null, just return normal output.
    * if the output is printable, attach the debug as a string and return it.
    * if debug is an object and has a __toString method, go with that.
    * otherwise, the debug must be an array or some other complex structure. 
    * use print_r to represent the debug.
    */
    public function __toString(){
        $out = parent::__toString();
        if( $this->debug === NULL ) return $out;
        return $out . self::DEBUG_HEADER . self::formatDebugOutput( self::stringify( $this->debug ) );
    }
    
   /**
    * utility function to make the output look pretty.
    */
    protected function formatDebugOutput( $str ){
        return str_replace("\n", "\n# ", $str ) . "\n";
    }
    
    public static function stringify( $var ){
        if( is_bool( $var ) ) return '(bool) ' . ($var ? 'true' : 'false');
        if( is_scalar( $var ) ) return $var;
        if( is_object( $var ) && method_exists($var, '__toString') ) return $var->__toString();
        if( is_array( $var ) ){
            foreach( $var as $k => $v ){
                $var[ $k ] = str_replace("\n", "\n             ", self::stringify( $v ));
            }
        }
        return print_r( $var, TRUE);
    }
} // EOC

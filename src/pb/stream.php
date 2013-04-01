<?php
namespace Riak\PB;
use Riak\PBMessageCodes;
use Riak\Exception;

class Stream {

    public function __construct( $fp ){
        $this->fp = $fp;
    }
    
    public function send( $request ){
        $this->write( $request );
        return $this->read();
    }
    
    function read(){
        $header = fread( $this->fp, 5 );
        $len = strlen( $header );
        if( $len != 5 ) throw new Exception("invalid stream response: got $len bytes in header, expecting 5");
        $len = unpack('N', substr($header, 0, 4));
        $class = __NAMESPACE__ .'\\' . PBMessageCodes::getName( ord( substr($header, 4) ) );
        $len = $len[1] - 1;
        $obj = new $class();
        $data = "";
        while( $len > 0 ){
            if( feof( $this->fp ) ) throw new Exception('stream closed unexpectedly');
            $buf = fread( $this->fp, $len );
            if( $buf === FALSE ) throw new Exception('stream closed unexpectedly');
            $len -= strlen( $buf );
            $data .= $buf;
        }
        $obj->parseFromString( $data );
        return $obj;
    }
    
    function write( $object ){
        $class = get_class( $object );
        $namespace = __NAMESPACE__ .'\\';
        $namespace_len = strlen($namespace);
        if( substr( $class, 0, $namespace_len ) != $namespace ) throw new Exception("invalid message: $class");
        $code = PBMessageCodes::getCode( substr( $class, $namespace_len) );
        $string = $object->serializeToString();
        $header = pack('N', strlen( $string ) + 1) . chr( $code );
        fwrite( $this->fp, $header . $string );
    }
}



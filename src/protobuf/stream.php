<?php
namespace Riak\ProtoBuf;
use Riak\PBMessageCodes;

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
        $class = 'Riak\ProtoBuf\\' . PBMessageCodes::getName( ord( substr($header, 4) ) );
        $len = $len[1] - 1;
        return new $class( $this->fp, $len );
    }
    
    function write( $object ){
        $class = get_class( $object );
        $namespace = __NAMESPACE__ .'\\';
        $namespace_len = strlen($namespace);
        if( substr( $class, 0, $namespace_len ) != $namespace ) throw new Exception("invalid message: $class");
        $code = PBMessageCodes::getCode( substr( $class, $namespace_len) );
        $header = pack('N', $object->size() + 1) . chr( $code );
        fwrite( $this->fp, $header );
        $object->write( $this->fp );
    }
}



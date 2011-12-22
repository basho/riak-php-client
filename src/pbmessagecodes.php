<?php
namespace Riak;

/**
* translate the Riak MessageCodes defined in the header of the proto file int class names and
* back again.
*/
class PBMessageCodes {

    protected static $index = array(
        0 => 'errorresp',
        1 => 'pingreq',
        2 => 'pingresp',
        3 => 'getclientidreq',
        4 => 'getclientidresp',
        5 => 'setclientidreq',
        6 => 'setclientidresp',
        7 => 'getserverinforeq',
        8 => 'getserverinforesp',
        9 => 'getreq',
        10 => 'getresp',
        11 => 'putreq',
        12 => 'putresp',
        13 => 'delreq',
        14 => 'delresp',
        15 => 'listbucketsreq',
        16 => 'listbucketsresp',
        17 => 'listkeysreq',
        18 => 'listkeysresp',
        19 => 'getbucketreq',
        20 => 'getbucketresp',
        21 => 'setbucketreq',
        22 => 'setbucketresp',
        23 => 'mapredreq',
        24 => 'mapredresp',
    );

    public static function getName( $code ){
        if( isset( self::$index[ $code ] ) ) return self::$index[ $code ];
        throw new Exception('invalid message code: ' . $code );
    }
    
    public static function getCode( $name ){
        $name = strtolower( $name );
        $keys = array_keys(self::$index, $name);
        if( ! $keys ) throw new Exception('invalid message name: ' . $name );
        $key = array_pop( $keys );
        return $key;
    }
    
    public static function getAll(){
        return self::$index;
    }
}
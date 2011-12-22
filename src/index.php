<?php

spl_autoload_register(function($class) {
    $class = strtolower( $class );
    if( substr( $class, 0, 5) == 'riak\\' ) 
        return include  __DIR__.'/'.strtr(substr($class, 5), '\\', '/').'.php';
});

class RiakClient extends Riak\Client {

}

<?php

spl_autoload_register(function($class) {
    if( substr( $class, 0, 8) == 'DrSlump\\' ) 
        return include  __DIR__.'/'.strtr(substr($class, 8), '\\', '/').'.php';
});


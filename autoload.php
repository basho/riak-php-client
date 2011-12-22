<?php
$dir = dirname(__FILE__);
if( version_compare(phpversion(), '5.3') < 1 ){
    include $dir . '/riak.php';
    return;
}

$file = $dir . '/riak.phar';
if( extension_loaded('phar') ) $file .= '.tar.gz';
if( file_exists( $file) ){
    include "phar://$file/index.php";
} else {
    include $dir . '/src/index.php';
}

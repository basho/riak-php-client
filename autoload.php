<?php
$dir = dirname(__FILE__);
if( version_compare(phpversion(), '5.3') < 1 ){
    include $dir . '/riak.php';
    return;
}

if( file_exists( $dir . '/riak.phar' ) ){
    include 'phar://' . $dir . '/riak.phar';
} else {
    include $dir . '/src/index.php';
}

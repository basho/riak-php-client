<?php
use Riak\Test;
use Riak\Client;
use Riak\Transport\ProtoBuf as Transport;
include __DIR__ . '/../../autoload.php';

$host = '127.0.0.1';
$port = 8087;

$config_file = __DIR__ . '/.config.php';
if( file_exists( $config_file ) ) include $config_file;

if( ! @fsockopen( $host, $port ) ){
    Test::plan('skip_all', 'could not connect to riak protocol buffer interface: ' . $host . ':' . $port);
}

$new_client = function() use ( $host, $port ) {
    return new Client( new Transport( $host, $port, $client_id = TRUE) );
};
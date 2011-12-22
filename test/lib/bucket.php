<?php
$client = $new_client();
use Riak\Test;

Test::plan(16);

$bucket = $client->bucket('test');

Test::ok( $bucket instanceof Riak\Bucket, 'got back a bucket object from the client');
Test::is( $bucket->getName(), 'test', 'name passed into the bucket from client factory call');
Test::is( $bucket->getR(50), 50, 'getR: passing in a non null value causes function to return value passed in');
Test::is( $bucket->getW(50), 50, 'getW: passing in a non null value causes function to return value passed in');
Test::is( $bucket->getDW(50), 50, 'getDW: passing in a non null value causes function to return value passed in');
Test::is( $bucket->getR(), $client->getR(), 'getr default matches client');
Test::is( $bucket->getW(), $client->getW(), 'getw default matches client');
Test::is( $bucket->getDW(), $client->getDW(), 'getdw default matches client');
Test::is( $bucket->setR(5), $bucket, 'setR returns bucket object');
Test::is( $bucket->getR(), 5, 'getR returns new value');
Test::is( $bucket->setW(5), $bucket, 'setW returns bucket object');
Test::is( $bucket->getW(), 5, 'getW returns new value');

$bucket = $client->bucket('testbucketprops');
$bucket->setNVal($nval = mt_rand(1, 5));

Test::is( $bucket->getNval(), $nval, 'set nval to ' . $nval); 
$bucket->setAllowMultiples(TRUE );
Test::is( $bucket->getAllowMultiples(), TRUE, 'turned on allow multiples'); 


$bucket->setAllowMultiples(FALSE);
Test::is( $bucket->getAllowMultiples(), FALSE, 'turned off allow multiples'); 

$bucket = $client->bucket('testbucketkeys');
$bucket->newObject('key1', 'test')->store();
$bucket->newObject('key2', 'test')->store();
$bucket->newObject('key3', 'test')->store();

$keys = $bucket->getKeys();
sort( $keys );
Test::is( $keys, array('key1', 'key2', 'key3'), 'fetched back a list of keys from a test bucket');


<?php
$client= $new_client();
$client->setR(0);
$client->setW(0);
use Riak\Test;

Test::plan(2);

$bucket = $client->bucket('benchmark');

$iterations = 1000;

$time_limit = floor($iterations / 100);


$start = microtime( TRUE );
print "# storing objects: ";
for( $i = 1; $i <= $iterations; $i++){
    $bucket->newObject("seq" . $i, array("a"=>rand(), "b"=>rand()))->store();
    if( $i % 100 == 1 ) print ".";
}
print "\n";

$elapsed = number_format(  microtime(TRUE) - $start, 5 );
Test::cmp_ok( $elapsed, '<', $time_limit, "stored $iterations objects in less than $time_limit secs. (actual: $elapsed)");

$start = microtime( TRUE );
print "# retrieving objects: ";
for( $i = 1; $i <= $iterations; $i++){
    $obj = $bucket->get("seq" . $i);
    if( $i % 100 == 1 ) print ".";
}
print "\n";

$elapsed = number_format(  microtime(TRUE) - $start, 5 );
Test::cmp_ok( $elapsed, '<', $time_limit, "read $iterations objects in less than $time_limit secs. (actual: $elapsed)");

//Test::debug( $buckets );
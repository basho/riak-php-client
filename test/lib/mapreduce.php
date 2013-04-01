<?php
$client= $new_client();
use Riak\Test;

Test::plan(7);


# Create the object...
$key = 'testmapreduce' . microtime(TRUE);
$bucket = $client->bucket("bucket");
$bucket->newObject($key, 2)->store();

# Run the map...
$result = $client->
add("bucket", $key)->
map("function (v) { return [JSON.parse(v.values[0].data)]; }") ->
run();
Test::is($result, array(2), 'create a javascript map function and run it against a given key');


# Run the map...
$result = $client->
add("bucket", $key)->
map("Riak.mapValuesJson") ->
run();
Test::is($result, array(2), 'use a built in map: Riak.mapValuesJson');


$keybase = 'testmapreduce' . microtime(TRUE);
$bucket->newObject($keybase . "foo", 2)->store();
$bucket->newObject($keybase . "bar", 3)->store();
$bucket->newObject($keybase . "baz", 4)->store();

# Run the map...
$result = $client->
add("bucket", $keybase . "foo")->
add("bucket", $keybase . "bar")->
add("bucket", $keybase . "baz")->
map("function (v) { return [1]; }") ->
reduce("Riak.reduceSum")->
run();
Test::is($result[0], 3, 'use custom map js function and built-in Riak.reduceSum against several keys');


# Run the map...
$result = $client->
add("bucket",  $keybase ."foo")->
add("bucket",  $keybase ."bar")->
add("bucket",  $keybase ."baz")->
map("Riak.mapValuesJson") ->
reduce("Riak.reduceSum")->
run();
Test::is($result, array(9), 'use built in Riak.mapValuesJson and Riak.reduceSum against several keys');


# Create the object...
$key = 'testmapreduce' . microtime(TRUE);
$bucket->newObject($key, 2)->store();

# Run the map...
$result = $client->
add("bucket", $key, 5)->
add("bucket", $key, 10)->
add("bucket", $key, 15)->
add("bucket", $key, -15)->
add("bucket", $key, -5)->
map("function(v, arg) { return [arg]; }")-> 
reduce("Riak.reduceSum")->
run();
Test::is($result, array(10), 'pass args to a map reduce function');


$keybase = 'testmapreduce' . microtime(TRUE);

# Create the object...
$bucket->newObject( $keybase . "foo", 2)->store();
$bucket->newObject( $keybase ."bar", 2)->store();
$bucket->newObject( $keybase . "baz", 4)->store();

# Run the map...
$result = $client->
add("bucket", $keybase . "foo")->
add("bucket", $keybase . "bar")->
add("bucket", $keybase . "baz")->
map(array("riak_kv_mapreduce", "map_object_value")) ->
reduce(array("riak_kv_mapreduce", "reduce_set_union"))->
run();
Test::is(count($result), 2, 'run an erlang map reduce');


# Create the object...
$key = 'testmapreduce' . microtime(TRUE);
$bucket->newObject($key, 2)->store();

$obj = $bucket->get($key);
$result = $obj->map("Riak.mapValuesJson")->run();
Test::is($result, array(2), 'run map reduce from an object');
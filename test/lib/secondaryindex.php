<?php
$client= $new_client();
use Riak\Test;

Test::plan(12);

$bucket = $client->bucket("indextest");
$bucket
  ->newObject("one", array("foo"=>1, "bar"=>"red"))
  ->addIndex("number", "int", 1)
  ->addIndex("text", "bin", "apple")
  ->addAutoIndex("foo", "int")
  ->addAutoIndex("bar", "bin")
  ->store();
$bucket
  ->newObject("two", array("foo"=>2, "bar"=>"green"))
  ->addIndex("number", "int", 2)
  ->addIndex("text", "bin", "avocado")
  ->addAutoIndex("foo", "int")
  ->addAutoIndex("bar", "bin")
  ->store();
$bucket
  ->newObject("three", array("foo"=>3, "bar"=>"blue"))
  ->addIndex("number", "int", 3)
  ->addIndex("text", "bin", "blueberry")
  ->addAutoIndex("foo", "int")
  ->addAutoIndex("bar", "bin")
  ->store();
$bucket
  ->newObject("four", array("foo"=>4, "bar"=>"orange"))
  ->addIndex("number", "int", 4)
  ->addIndex("text", "bin", "citrus")
  ->addAutoIndex("foo", "int")
  ->addAutoIndex("bar", "bin")
  ->store();
$bucket
  ->newObject("five", array("foo"=>5, "bar"=>"yellow"))
  ->addIndex("number", "int", 5)
  ->addIndex("text", "bin", "banana")
  ->addAutoIndex("foo", "int")
  ->addAutoIndex("bar", "bin")
  ->store();

$bucket
  ->newObject("six", array("foo"=>6, "bar"=>"purple"))
  ->addIndex("number", "int", 6)
  ->addIndex("number", "int", 7)
  ->addIndex("number", "int", 8)
  ->setIndex("text", "bin", array("x","y","z"))
  ->store();

# Exact matches
$results = $bucket->indexSearch("number", "int", 5);
Test::is(count($results), 1, 'run an integer search');

$results = $bucket->indexSearch("text", "bin", "apple");
Test::is(count($results), 1, 'run a binary search');

# Range searches 
$results = $bucket->indexSearch("foo", "int", 1, 3);
Test::is(count($results), 3, 'run a range search');

$results = $bucket->indexSearch("bar", "bin", "blue", "orange");
Test::is(count($results), 3, 'search for range across binary index');

# Test duplicate key de-duping
$results = $bucket->indexSearch("number", "int", 6, 8, true);
Test::is(count($results), 1, 'verify key de-duping');

$results = $bucket->indexSearch("text", "bin", "x", "z", true);
Test::is(count($results), 1, 'run another range search on text binary index');

# Test auto indexes don't leave cruft indexes behind, and regular
# indexes are preserved
$object = $bucket->get("one");
$object->setData(array("foo"=>9, "bar"=>"plaid"));
$object->store();
  
  # Auto index updates
  $results = $bucket->indexSearch("foo", "int", 9);
  Test::is(count($results), 1, 'make sure auto-index works');
  
  # Auto index leaves no cruft
  $results = $bucket->indexSearch("foo", "int", 1);
  Test::is(count($results), 0, 'make sure auto-index leaves no cruft behind on changes');
  
  # Normal index is preserved
  $results = $bucket->indexSearch("number", "int", 1);
  Test::is(count($results), 1, 'make sure normal index is preserved');
  
  
  # Test proper collision handling on autoIndex and regular index on same field
  $bucket
    ->newObject("seven", array("foo"=>7))
    ->addAutoIndex("foo", "int")
    ->addIndex("foo", "int", 7)
    ->store();
  
  $results = $bucket->indexSearch("foo", "int", 7);
  Test::is(count($results), 1, 'test collision handling autoIndex 1');
  
  $object = $bucket->get("seven");
  $object->setData(array("foo"=>8));
  $object->store();
  
  $results = $bucket->indexSearch("foo", "int", 8);
  Test::is(count($results), 1, 'test collision handling autoIndex 2');
  
  $results = $bucket->indexSearch("foo", "int", 7);
  Test::is(count($results), 1, 'test collision handling autoIndex 3');


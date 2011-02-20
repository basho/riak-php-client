#!/usr/bin/env php

<?php

require_once 'riak.php';

define('HOST', 'localhost');
define('PORT', 8098);
define('VERBOSE', true);


print("Starting Unit Tests\n---\n");

test("testIsAlive");
test("testStoreAndGet");
test("testStoreAndGetWithoutKey");
test("testBinaryStoreAndGet");
test("testMissingObject");
test("testDelete");
test("testSetBucketProperties");
test("testSiblings");

test("testJavascriptSourceMap");
test("testJavascriptNamedMap");
test("testJavascriptSourceMapReduce");
test("testJavascriptNamedMapReduce");
test("testJavascriptArgMapReduce");

test("testErlangMapReduce");
test("testMapReduceFromObject");

test("testStoreAndGetLinks");
test("testLinkWalking");

test("testSearchIntegration");

test_summary();


/* BEGIN UNIT TESTS */

function testIsAlive() {
  $client = new RiakClient(HOST, PORT);
  test_assert($client->isAlive());
}

function testStoreAndGet() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  
  $rand = rand();
  $obj = $bucket->newObject('foo', $rand);
  $obj->store();

  $obj = $bucket->get('foo');
  test_assert($obj->exists());
  test_assert($obj->getBucket()->getName() == 'bucket');
  test_assert($obj->getKey() == 'foo');
  test_assert($obj->getData() == $rand);
}

function testStoreAndGetWithoutKey() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  
  $rand = rand();
  $obj = $bucket->newObject(null, $rand);
  $obj->store();
  
  $key = $obj->key;

  $obj = $bucket->get($key);
  test_assert($obj->exists());
  test_assert($obj->getBucket()->getName() == 'bucket');
  test_assert($obj->getKey() == $key);
  test_assert($obj->getData() == $rand);
}

function testBinaryStoreAndGet() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');

  # Store as binary, retrieve as binary, then compare...
  $rand = rand();
  $obj = $bucket->newBinary('foo1', $rand);
  $obj->store();
  $obj = $bucket->getBinary('foo1');
  test_assert($obj->exists());
  test_assert($obj->getData() == $rand);

  # Store as JSON, retrieve as binary, JSON-decode, then compare...
  $data = array(rand(), rand(), rand());
  $obj = $bucket->newObject('foo2', $data);
  $obj->store();
  $obj = $bucket->getBinary('foo2');
  test_assert($data == json_decode($obj->getData()));
}

function testMissingObject() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  $obj = $bucket->get("missing");
  test_assert(!$obj->exists());
  test_assert($obj->getData() == NULL);
}

function testDelete() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  
  $rand = rand();
  $obj = $bucket->newObject('foo', $rand);
  $obj->store();

  $obj = $bucket->get('foo');
  test_assert($obj->exists());

  $obj->delete();
  $obj->reload();
  test_assert(!$obj->exists());
}

function testSetBucketProperties() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');

  # Test setting allow mult...
  $bucket->setAllowMultiples(TRUE);
  test_assert($bucket->getAllowMultiples());

  # Test setting nval...
  $bucket->setNVal(3);
  test_assert($bucket->getNVal() == 3);

  # Test setting multiple properties...
  $bucket->setProperties(array("allow_mult"=>FALSE, "n_val"=>2));
  test_assert(!$bucket->getAllowMultiples());
  test_assert($bucket->getNVal() == 2);
}

function testSiblings() {
  # Set up the bucket, clear any existing object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('multiBucket');
  $bucket->setAllowMultiples('true');
  $obj = $bucket->get('foo');
  $obj->delete();

 # Store the same object multiple times...
  for ($i=0; $i<5; $i++) {
    $client = new RiakClient(HOST, PORT);
    $bucket = $client->bucket('multiBucket');
    $obj = $bucket->newObject('foo', rand());
    $obj->store();
  }

  # Make sure the object has 5 siblings...
  test_assert($obj->hasSiblings());
  test_assert($obj->getSiblingCount() == 5);

  # Test getSibling()/getSiblings()...
  $siblings = $obj->getSiblings();
  $obj3 = $obj->getSibling(3);
  test_assert($siblings[3]->getData() == $obj3->getData());

  # Resolve the conflict, and then do a get...
  $obj3 = $obj->getSibling(3);
  $obj3->store();
  
  $obj->reload();
  test_assert($obj->getData() == $obj3->getData());
  
  # Clean up for next test...
  $obj->delete();
}

function testJavascriptSourceMap() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    map("function (v) { return [JSON.parse(v.values[0].data)]; }") ->
    run();
  test_assert($result == array(2));
}

function testJavascriptNamedMap() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    map("Riak.mapValuesJson") ->
    run();
  test_assert($result == array(2));
}

function testJavascriptSourceMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();
  $bucket->newObject("bar", 3)->store();
  $bucket->newObject("baz", 4)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    add("bucket", "bar")->
    add("bucket", "baz")->
    map("function (v) { return [1]; }") ->
    reduce("function (v) { return [v.length]; }")->
    run();
  test_assert($result[0] == 3);
}

function testJavascriptNamedMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();
  $bucket->newObject("bar", 3)->store();
  $bucket->newObject("baz", 4)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    add("bucket", "bar")->
    add("bucket", "baz")->
    map("Riak.mapValuesJson") ->
    reduce("Riak.reduceSum")->
    run();
  test_assert($result == array(9));
}

function testJavascriptBucketMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket_" . rand());
  $bucket->newObject("foo", 2)->store();
  $bucket->newObject("bar", 3)->store();
  $bucket->newObject("baz", 4)->store();

  # Run the map...
  $result = $client->
    add($bucket->name)->
    map("Riak.mapValuesJson") ->
    reduce("Riak.reduceSum")->
    run();
  test_assert($result == array(9));
}

function testJavascriptArgMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo", 5)->
    add("bucket", "foo", 10)->
    add("bucket", "foo", 15)->
    add("bucket", "foo", -15)->
    add("bucket", "foo", -5)->
    map("function(v, arg) { return [arg]; }")-> 
    reduce("Riak.reduceSum")->
    run();
  test_assert($result == array(10));
}

function testErlangMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();
  $bucket->newObject("bar", 2)->store();
  $bucket->newObject("baz", 4)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    add("bucket", "bar")->
    add("bucket", "baz")->
    map(array("riak_kv_mapreduce", "map_object_value")) ->
    reduce(array("riak_kv_mapreduce", "reduce_set_union"))->
    run();
  test_assert(count($result) == 2);
}

function testMapReduceFromObject() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();
 
  $obj = $bucket->get("foo");
  $result = $obj->map("Riak.mapValuesJson")->run();
  test_assert($result = array(2));
}


function testStoreAndGetLinks() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->
    addLink($bucket->newObject("foo1"))->
    addLink($bucket->newObject("foo2"), "tag")->
    addLink($bucket->newObject("foo3"), "tag2!@#$%^&*")->
    store();

  $obj = $bucket->get("foo");
  $links = $obj->getLinks();
  test_assert(count($links) == 3);
}

function testLinkWalking() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->
    addLink($bucket->newObject("foo1", "test1")->store())->
    addLink($bucket->newObject("foo2", "test2")->store(), "tag")->
    addLink($bucket->newObject("foo3", "test3")->store(), "tag2!@#$%^&*")->
    store();
  
  $obj = $bucket->get("foo");
  $results = $obj->link("bucket")->run();
  test_assert(count($results) == 3);

  $results = $obj->link("bucket", "tag")->run();
  test_assert(count($results) == 1);
}

function testSearchIntegration() {
  # Create some objects to search across...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("searchbucket");
  $bucket->newObject("one", array("foo"=>"one", "bar"=>"red"))->store();
  $bucket->newObject("two", array("foo"=>"two", "bar"=>"green"))->store();
  $bucket->newObject("three", array("foo"=>"three", "bar"=>"blue"))->store();
  $bucket->newObject("four", array("foo"=>"four", "bar"=>"orange"))->store();
  $bucket->newObject("five", array("foo"=>"five", "bar"=>"yellow"))->store();

  # Run some operations...
  $results = $client->search("searchbucket", "foo:one OR foo:two")->run();
  if (count($results) == 0) {
    print "\n\nNot running test \"testSearchIntegration()\".\n";
    print "Please ensure that you have installed the Riak Search hook on bucket \"searchbucket\" by running \"bin/search-cmd install searchbucket\".\n\n";
    return;
  }
  test_assert(count($results) == 2);

  $results = $client->search("searchbucket", "(foo:one OR foo:two OR foo:three OR foo:four) AND (NOT bar:green)")->run();
  test_assert(count($results) == 3);
}

/* BEGIN UNIT TEST FRAMEWORK */
$test_pass = 0; $test_fail = 0;

function test($method) {
  global $test_pass, $test_fail;
  try {
    $method(); 
    $test_pass++;
    print "  [.] TEST PASSED: $method\n";
  } catch (Exception $e) {
    $test_fail++;
    print "  [X] TEST FAILED: $method\n";
    if (VERBOSE) {
      throw $e;
    }
  }
}

function test_summary() {
  global $test_pass, $test_fail;
  if ($test_fail == 0) {
    print "\nSUCCESS: Passed all $test_pass tests.\n";
  } else {
    $test_total = $test_pass + $test_fail;
    print "\nFAILURE: Failed $test_fail of $test_total tests!";
  }
}

function test_assert($bool) {
  if (!$bool) throw new Exception("Test failed.");
}

/* END UNIT FRAMEWORK */
?>

#!/usr/bin/env php

<?php

require_once 'riak.php';

define('HOST', 'localhost');
define('PORT', 8098);
define('VERBOSE', true);


print("Starting Unit Tests\n---\n");

test("testIsAlive");
test("testPrepareIsAlive");
test("testStoreAndGet");
test("testPrepareStoreAndGet");
test("testStoreAndGetWithoutKey");
test("testPrepareStoreAndGetWithoutKey");
test("testBinaryStoreAndGet");
test("testPrepareBinaryStoreAndGet");
test("testMissingObject");
test("testPrepareMissingObject");
test("testDelete");
test("testPrepareDelete");
test("testSetBucketProperties");
test("testPrepareSetBucketProperties");
test("testSiblings");
test("testPrepareSiblings");

test("testJavascriptSourceMap");
test("testPrepareJavascriptSourceMap");
test("testJavascriptNamedMap");
test("testPrepareJavascriptNamedMap");
test("testJavascriptSourceMapReduce");
test("testPrepareJavascriptSourceMapReduce");
test("testJavascriptNamedMapReduce");
test("testPrepareJavascriptNamedMapReduce");
test("testJavascriptArgMapReduce");
test("testPrepareJavascriptArgMapReduce");

test("testErlangMapReduce");
test("testPrepareErlangMapReduce");

test("testMapReduceFromObject");
test("testPrepareMapReduceFromObject");

test("testKeyFilter");
test("testPrepareKeyFilter");
test("testKeyFilterOperator");
test("testPrepareKeyFilterOperator");

test("testStoreAndGetLinks");
test("testPrepareStoreAndGetLinks");

test("testLinkWalking");
test("testPrepareLinkWalking");
test("testSearchIntegration");
test("testPrepareSearchIntegration");

test("testSecondaryIndexes");
test("testPrepareSecondaryIndexes");

test("testMetaData");
test("testPrepareMetaData");

test_summary();


/* BEGIN UNIT TESTS */

function testIsAlive() {
  $client = new RiakClient(HOST, PORT);
  test_assert($client->isAlive());
}

function testPrepareIsAlive(){
  $client = new RiakClient(HOST, PORT);
  test_assert($client->prepare_isAlive()->send()->result);
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

function testPrepareStoreAndGet() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  
  $rand = rand();
  $obj = $bucket->newObject('foo', $rand);
  $obj->prepare_store()->send();

  $obj = $bucket->newObject('foo');
  $obj->prepare_reload()->send();
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

function testPrepareStoreAndGetWithoutKey() {
  $client = new RiakClient(HOST, PORT);
  $client->defer = TRUE;
  $bucket = $client->bucket('bucket');
  
  $rand = rand();
  $obj = $bucket->newObject(null, $rand);
  $obj->prepare_store()->send();
  
  $key = $obj->key;

  $obj = $bucket->newObject($key);
  $obj->prepare_reload()->send();
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

function testPrepareBinaryStoreAndGet() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');

  # Store as binary, retrieve as binary, then compare...
  $rand = rand();
  $obj = $bucket->newBinary('foo1', $rand);
  $obj->prepare_store()->send();
  $obj = $bucket->newBinary('foo1');
  $obj->prepare_reload()->send();
  test_assert($obj->exists());
  test_assert($obj->getData() == $rand);

  # Store as JSON, retrieve as binary, JSON-decode, then compare...
  $data = array(rand(), rand(), rand());
  $obj = $bucket->newObject('foo2', $data);
  $obj->prepare_store()->send();
  $obj = $bucket->binary('foo2');
  $obj->prepare_reload()->send();
  test_assert($data == json_decode($obj->getData()));
}

function testMissingObject() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  $obj = $bucket->get("missing");
  test_assert(!$obj->exists());
  test_assert($obj->getData() == NULL);
}

function testPrepareMissingObject() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  $obj = $bucket->object("missing");
  $obj->prepare_reload()->send();
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

function testPrepareDelete() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  
  $rand = rand();
  $obj = $bucket->newObject('foo', $rand);
  $obj->prepare_store()->send();

  $obj = $bucket->object('foo');
  $obj->prepare_reload()->send();
  test_assert($obj->exists());

  $obj->prepare_delete()->send();
  $obj->prepare_reload()->send();
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

function testPrepareSetBucketProperties() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('bucket');
  
  $bucket->prepare_setProperties(array("allow_mult"=>TRUE, "n_val"=>3))->send();
  $props = $bucket->prepare_getProperties()->send()->result;

  test_assert( $props['allow_mult'] == TRUE );
  test_assert( $props['n_val'] == 3 );

  $bucket->prepare_setProperties(array("allow_mult"=>FALSE, "n_val"=>2))->send();
  $props = $bucket->prepare_getProperties()->send()->result;
  
  test_assert( $props['allow_mult'] == FALSE );
  test_assert( $props['n_val'] == 2 );
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
  test_assert($obj->getSiblingCount() >= 5);

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

function testPrepareSiblings() {
  # Set up the bucket, clear any existing object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket('multiBucket');
  $bucket->setAllowMultiples('true');
  $obj = $bucket->get('foo');
  $obj->delete();
  $pool = $client->pool();
  # Store the same object multiple times...
  $r = array();
  for ($i=0; $i<5; $i++) {
    $obj = $bucket->newObject('foo', rand());
    $r[] = $pool->add( $obj->prepare_store() );
  }
  $pool->finish();

  $obj = $bucket->get('foo');
  
  # Make sure the object has 5 siblings...
  test_assert($obj->hasSiblings());
  test_assert($obj->getSiblingCount() >= 5);

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

function testPrepareJavascriptSourceMap() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->prepare_store()->send();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    map("function (v) { return [JSON.parse(v.values[0].data)]; }") ->
    prepare_run()->send()->result;
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

function testPrepareJavascriptNamedMap() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->store();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    map("Riak.mapValuesJson") ->
    prepare_run()->send()->result;
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
    reduce("Riak.reduceSum")->
    run();
  test_assert($result[0] == 3);
}

function testPrepareJavascriptSourceMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $pool = $client->pool();
  $pool->add( $bucket->newObject("foo", 2)->prepare_store() );
  $pool->add( $bucket->newObject("bar", 3)->prepare_store() );
  $pool->add( $bucket->newObject("baz", 4)->prepare_store() );
  $pool->finish();

  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    add("bucket", "bar")->
    add("bucket", "baz")->
    map("function (v) { return [1]; }") ->
    reduce("Riak.reduceSum")->
    prepare_run()->send()->result;
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

function testPrepareJavascriptNamedMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $pool = $client->pool();
  $pool->add( $bucket->newObject("foo", 2)->prepare_store() );
  $pool->add( $bucket->newObject("bar", 3)->prepare_store() );
  $pool->add( $bucket->newObject("baz", 4)->prepare_store() );
  $pool->finish();
  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    add("bucket", "bar")->
    add("bucket", "baz")->
    map("Riak.mapValuesJson") ->
    reduce("Riak.reduceSum")->
    prepare_run()->send()->result;
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

function testPrepareJavascriptBucketMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket_" . rand());
  $pool = $client->pool();
  $pool->add( $bucket->newObject("foo", 2)->store() );
  $pool->add( $bucket->newObject("bar", 3)->store() );
  $pool->add( $bucket->newObject("baz", 4)->store() );
  $pool->finish();

  # Run the map...
  $result = $client->
    add($bucket->name)->
    map("Riak.mapValuesJson") ->
    reduce("Riak.reduceSum")->
    prepare_run()->send()->result;
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

function testPrepareJavascriptArgMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->prepare_store()->send();

  # Run the map...
  $result = $client->
    add("bucket", "foo", 5)->
    add("bucket", "foo", 10)->
    add("bucket", "foo", 15)->
    add("bucket", "foo", -15)->
    add("bucket", "foo", -5)->
    map("function(v, arg) { return [arg]; }")-> 
    reduce("Riak.reduceSum")->
    prepare_run()->send()->result;
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

function testPrepareErlangMapReduce() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $pool = $client->pool();
  $pool->add( $bucket->newObject("foo", 2)->prepare_store() );
  $pool->add( $bucket->newObject("bar", 2)->prepare_store() );
  $pool->add( $bucket->newObject("baz", 4)->prepare_store() );
  $pool->finish();


  # Run the map...
  $result = $client->
    add("bucket", "foo")->
    add("bucket", "bar")->
    add("bucket", "baz")->
    map(array("riak_kv_mapreduce", "map_object_value")) ->
    reduce(array("riak_kv_mapreduce", "reduce_set_union"))->
    prepare_run()->send()->result;
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

function testPrepareMapReduceFromObject() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->prepare_store()->send();
 
  $obj = $bucket->get("foo");
  $obj->prepare_reload()->send();
  $result = $obj->map("Riak.mapValuesJson")->prepare_run()->send()->result;
  test_assert($result = array(2));
}


function testKeyFilter() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("filter_bucket");
  $bucket->newObject("foo_one",   array("foo"=>"one"  ))->store();
  $bucket->newObject("foo_two",   array("foo"=>"two"  ))->store();
  $bucket->newObject("foo_three", array("foo"=>"three"))->store();
  $bucket->newObject("foo_four",  array("foo"=>"four" ))->store();
  $bucket->newObject("moo_five",  array("foo"=>"five" ))->store();
  
  $mapred = $client
  	->add($bucket->name)
  	->key_filter(array('tokenize', '_', 1), array('eq', 'foo'));
  $results = $mapred->run();
  test_assert(count($results) == 4);
}


function testPrepareKeyFilter() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("filter_bucket");
  $pool = $client->pool();
  $pool->add($bucket->newObject("foo_one",   array("foo"=>"one"  ))->prepare_store());
  $pool->add($bucket->newObject("foo_two",   array("foo"=>"two"  ))->prepare_store());
  $pool->add($bucket->newObject("foo_three", array("foo"=>"three"))->prepare_store());
  $pool->add($bucket->newObject("foo_four",  array("foo"=>"four" ))->prepare_store());
  $pool->add($bucket->newObject("moo_five",  array("foo"=>"five" ))->prepare_store());
  $pool->finish();
  $mapred = $client
  	->add($bucket->name)
  	->key_filter(array('tokenize', '_', 1), array('eq', 'foo'));
  $results = $mapred->prepare_run()->send()->result;
  test_assert(count($results) == 4);
}

function testKeyFilterOperator() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("filter_bucket");
  $bucket->newObject("foo_one",   array("foo"=>"one"  ))->store();
  $bucket->newObject("foo_two",   array("foo"=>"two"  ))->store();
  $bucket->newObject("foo_three", array("foo"=>"three"))->store();
  $bucket->newObject("foo_four",  array("foo"=>"four" ))->store();
  $bucket->newObject("moo_five",  array("foo"=>"five" ))->store();
  
  $mapred = $client
  	->add($bucket->name)
  	->key_filter(array('starts_with', 'foo'))
  	->key_filter_or(array('ends_with', 'five'));
  $results = $mapred->run();
  test_assert(count($results) == 5);
}

function testPrepareKeyFilterOperator() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("filter_bucket");
  $pool = $client->pool();
  $pool->add($bucket->newObject("foo_one",   array("foo"=>"one"  ))->prepare_store());
  $pool->add($bucket->newObject("foo_two",   array("foo"=>"two"  ))->prepare_store());
  $pool->add($bucket->newObject("foo_three", array("foo"=>"three"))->prepare_store());
  $pool->add($bucket->newObject("foo_four",  array("foo"=>"four" ))->prepare_store());
  $pool->add($bucket->newObject("moo_five",  array("foo"=>"five" ))->prepare_store());
  
  $pool->finish();
  
  $mapred = $client
  	->add($bucket->name)
  	->key_filter(array('starts_with', 'foo'))
  	->key_filter_or(array('ends_with', 'five'));
  $results = $mapred->prepare_run()->send()->result;
  test_assert(count($results) == 5);
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


function testPrepareStoreAndGetLinks() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("bucket");
  $bucket->newObject("foo", 2)->
    addLink($bucket->newObject("foo1"))->
    addLink($bucket->newObject("foo2"), "tag")->
    addLink($bucket->newObject("foo3"), "tag2!@#$%^&*")->
    prepare_store()->send();

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

function testPrepareLinkWalking() {
  # Create the object...
  $client = new RiakClient(HOST, PORT);
  $client->defer = TRUE;
  $bucket = $client->bucket("bucket");
  
  $pool = $client->pool();
  $objects = array();
  $objects[] = $bucket->newObject("foo1", "test1");
  $objects[] = $bucket->newObject("foo2", "test2");
  $objects[] = $bucket->newObject("foo3", "test3");
  
  foreach( $objects as $object ){
    $pool->add( $object->prepare_store() );
  }
  
  $pool->finish();
    
  $bucket->newObject("foo", 2)->
    addLink($objects[0])->
    addLink($objects[1], "tag")->
    addLink($objects[2], "tag2!@#$%^&*")->
    prepare_store()->send();
  
  $obj = $bucket->get("foo");
  $results = $obj->link("bucket")->prepare_run()->send()->result;
  
  test_assert(count($results) == 3);

  $results = $obj->link("bucket", "tag")->prepare_run()->send()->result;
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

function testPrepareSearchIntegration() {
  # Create some objects to search across...
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("searchbucket");
  $pool = $client->pool();
  
  $pool->add( $bucket->newObject("one", array("foo"=>"one", "bar"=>"red"))->prepare_store() );
  $pool->add( $bucket->newObject("two", array("foo"=>"two", "bar"=>"green"))->prepare_store() );
  $pool->add( $bucket->newObject("three", array("foo"=>"three", "bar"=>"blue"))->prepare_store() );
  $pool->add( $bucket->newObject("four", array("foo"=>"four", "bar"=>"orange"))->prepare_store() );
  $pool->add( $bucket->newObject("five", array("foo"=>"five", "bar"=>"yellow"))->prepare_store() );
  $pool->finish();

  # Run some operations...
  $results = $client->search("searchbucket", "foo:one OR foo:two")->prepare_run()->send()->result;
  if (count($results) == 0) {
    print "\n\nNot running test \"testSearchIntegration()\".\n";
    print "Please ensure that you have installed the Riak Search hook on bucket \"searchbucket\" by running \"bin/search-cmd install searchbucket\".\n\n";
    return;
  }
  test_assert(count($results) == 2);

  $results = $client->search("searchbucket", "(foo:one OR foo:two OR foo:three OR foo:four) AND (NOT bar:green)")->prepare_run()->send()->result;
  test_assert(count($results) == 3);
}

function testSecondaryIndexes() {
  $client = new RiakClient(HOST, PORT);
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
  test_assert(count($results) == 1);
  
  $results = $bucket->indexSearch("text", "bin", "apple");
  test_assert(count($results) == 1);
  
  # Range searches 
  $results = $bucket->indexSearch("foo", "int", 1, 3);
  test_assert(count($results) == 3);
  
  $results = $bucket->indexSearch("bar", "bin", "blue", "orange");
  test_assert(count($results) == 3);
  
  # Test duplicate key de-duping
  $results = $bucket->indexSearch("number", "int", 6, 8, true);
  test_assert(count($results) == 1);
  
  $results = $bucket->indexSearch("text", "bin", "x", "z", true);
  test_assert(count($results) == 1);
  
  # Test auto indexes don't leave cruft indexes behind, and regular
  # indexes are preserved
  $object = $bucket->get("one");
  $object->setData(array("foo"=>9, "bar"=>"plaid"));
  $object->store();
  
  # Auto index updates
  $results = $bucket->indexSearch("foo", "int", 9);
  test_assert(count($results) == 1);
  
  # Auto index leaves no cruft
  $results = $bucket->indexSearch("foo", "int", 1);
  test_assert(count($results) == 0);
  
  # Normal index is preserved
  $results = $bucket->indexSearch("number", "int", 1);
  test_assert(count($results) == 1);
  
  
  # Test proper collision handling on autoIndex and regular index on same field
  $bucket
    ->newObject("seven", array("foo"=>7))
    ->addAutoIndex("foo", "int")
    ->addIndex("foo", "int", 7)
    ->store();
  
  $results = $bucket->indexSearch("foo", "int", 7);
  test_assert(count($results) == 1);
  
  $object = $bucket->get("seven");
  $object->setData(array("foo"=>8));
  $object->store();
  
  $results = $bucket->indexSearch("foo", "int", 8);
  test_assert(count($results) == 1);
  
  $results = $bucket->indexSearch("foo", "int", 7);
  test_assert(count($results) == 1);
  
}



function testPrepareSecondaryIndexes() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("indextest");
  $pool = $client->pool();
  $pool->add(
  $bucket
    ->newObject("one", array("foo"=>1, "bar"=>"red"))
    ->addIndex("number", "int", 1)
    ->addIndex("text", "bin", "apple")
    ->addAutoIndex("foo", "int")
    ->addAutoIndex("bar", "bin")
    ->prepare_store());
  $pool->add(
  $bucket
    ->newObject("two", array("foo"=>2, "bar"=>"green"))
    ->addIndex("number", "int", 2)
    ->addIndex("text", "bin", "avocado")
    ->addAutoIndex("foo", "int")
    ->addAutoIndex("bar", "bin")
    ->prepare_store());
  $pool->add(
  $bucket
    ->newObject("three", array("foo"=>3, "bar"=>"blue"))
    ->addIndex("number", "int", 3)
    ->addIndex("text", "bin", "blueberry")
    ->addAutoIndex("foo", "int")
    ->addAutoIndex("bar", "bin")
    ->prepare_store());
  $pool->add(
  $bucket
    ->newObject("four", array("foo"=>4, "bar"=>"orange"))
    ->addIndex("number", "int", 4)
    ->addIndex("text", "bin", "citrus")
    ->addAutoIndex("foo", "int")
    ->addAutoIndex("bar", "bin")
    ->prepare_store());
  $pool->add(
  $bucket
    ->newObject("five", array("foo"=>5, "bar"=>"yellow"))
    ->addIndex("number", "int", 5)
    ->addIndex("text", "bin", "banana")
    ->addAutoIndex("foo", "int")
    ->addAutoIndex("bar", "bin")
    ->prepare_store());
  $pool->add(
  $bucket
    ->newObject("six", array("foo"=>6, "bar"=>"purple"))
    ->addIndex("number", "int", 6)
    ->addIndex("number", "int", 7)
    ->addIndex("number", "int", 8)
    ->setIndex("text", "bin", array("x","y","z"))
    ->prepare_store());

  $pool->finish();
  
  # Exact matches
  $requests = array();
  
  $requests['exact1'] = $pool->add($bucket->prepare_indexSearch("number", "int", 5));
  $requests['exact2'] = $pool->add($bucket->prepare_indexSearch("text", "bin", "apple"));
  $requests['range1'] = $pool->add($bucket->prepare_indexSearch("foo", "int", 1, 3));
  $requests['range2'] = $pool->add($bucket->prepare_indexSearch("bar", "bin", "blue", "orange"));
  $requests['dedupe1'] = $pool->add($bucket->prepare_indexSearch("number", "int", 6, 8, true));
  $requests['dedupe2'] = $pool->add($bucket->prepare_indexSearch("text", "bin", "x", "z", true));

  $pool->finish();
  
  $result = array();
  foreach( $requests as $k =>$request ){
    $result[ $k ] = $request->response->result;
  }
  
  test_assert(count($result['exact1']) == 1);
  test_assert(count($result['exact2']) == 1);
  test_assert(count($result['range1']) == 3);
  test_assert(count($result['range2']) == 3);
  test_assert(count($result['dedupe1']) == 1);
  test_assert(count($result['dedupe2']) == 1);
  
  # Test auto indexes don't leave cruft indexes behind, and regular
  # indexes are preserved
  $object = $bucket->get("one");
  $object->setData(array("foo"=>9, "bar"=>"plaid"));
  $object->store();
  
  # Auto index updates
  $results = $bucket->prepare_indexSearch("foo", "int", 9)->send()->result;
  test_assert(count($results) == 1);
  
  # Auto index leaves no cruft
  $results = $bucket->prepare_indexSearch("foo", "int", 1)->send()->result;
  test_assert(count($results) == 0);
  
  # Normal index is preserved
  $results = $bucket->prepare_indexSearch("number", "int", 1)->send()->result;
  test_assert(count($results) == 1);
  
  
  # Test proper collision handling on autoIndex and regular index on same field
  $bucket
    ->newObject("seven", array("foo"=>7))
    ->addAutoIndex("foo", "int")
    ->addIndex("foo", "int", 7)
    ->store();
  
  $results = $bucket->prepare_indexSearch("foo", "int", 7)->send()->result;
  test_assert(count($results) == 1);
  
  $object = $bucket->get("seven");
  $object->setData(array("foo"=>8));
  $object->store();
  
  $results = $bucket->prepare_indexSearch("foo", "int", 8)->send()->result;
  test_assert(count($results) == 1);
  
  $results = $bucket->prepare_indexSearch("foo", "int", 7)->send()->result;
  test_assert(count($results) == 1);
  
}

function testMetaData() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("metatest");

  # Set some meta
  $bucket->newObject("metatest", array("foo"=>'bar'))
    ->setMeta("foo", "bar")->store();
  
  # Test that we load the meta back
  $object = $bucket->get("metatest");
  test_assert($object->getMeta("foo") == "bar");
  
  # Test that the meta is preserved when we rewrite the object
  $bucket->get("metatest")->store();
  $object = $bucket->get("metatest");
  test_assert($object->getMeta("foo") == "bar");
  
  # Test that we remove meta
  $object->removeMeta("foo")->store();
  $anotherObject = $bucket->get("metatest");
  test_assert($anotherObject->getMeta("foo") === null);
}

function testPrepareMetaData() {
  $client = new RiakClient(HOST, PORT);
  $bucket = $client->bucket("metatest");

  # Set some meta
  $bucket->newObject("metatest", array("foo"=>'bar'))
    ->setMeta("foo", "bar")->prepare_store()->send();
  
  # Test that we load the meta back
  $object = $bucket->get("metatest");
  test_assert($object->getMeta("foo") == "bar");
  
  # Test that the meta is preserved when we rewrite the object
  $bucket->get("metatest")->prepare_store()->send();
  $object = $bucket->get("metatest");
  test_assert($object->getMeta("foo") == "bar");
  
  # Test that we remove meta
  $object->removeMeta("foo")->prepare_store()->send();
  $anotherObject = $bucket->get("metatest");
  test_assert($anotherObject->getMeta("foo") === null);
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
      print "        " . str_replace("\n", "\n        ", $e->getTraceAsString()) . "\n";
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

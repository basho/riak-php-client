<?php
$client= $new_client();
use Riak\Test;



# Create some objects to search across...
$bucket = $client->bucket("searchbucket");
$bucket->newObject("one", array("foo"=>"one", "bar"=>"red"))->store();
$bucket->newObject("two", array("foo"=>"two", "bar"=>"green"))->store();
$bucket->newObject("three", array("foo"=>"three", "bar"=>"blue"))->store();
$bucket->newObject("four", array("foo"=>"four", "bar"=>"orange"))->store();
$bucket->newObject("five", array("foo"=>"five", "bar"=>"yellow"))->store();

# Run some operations...
try {
    $results = $client->search("searchbucket", "foo:one OR foo:two")->run();
} catch ( Exception $e ){
    Test::plan('skip_all', "Please ensure that you have installed the Riak Search hook on bucket \"searchbucket\" by running \"bin/search-cmd install searchbucket");
}

Test::plan(2);
Test::is(count($results), 2, 'simple search');

$results = $client->search("searchbucket", "(foo:one OR foo:two OR foo:three OR foo:four) AND (NOT bar:green)")->run();
Test::is(count($results) == 3, 'more complex search');
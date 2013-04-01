<?php
$client= $new_client();
use Riak\Test;

Test::plan(3);


# Create the object...
$prefix = 'link' . microtime(TRUE);

# Create the object...
$bucket = $client->bucket("bucket");
$bucket->newObject($prefix . "foo", 2)->
addLink($bucket->newObject($prefix ."foo1"))->
addLink($bucket->newObject($prefix ."foo2"), "tag")->
addLink($bucket->newObject($prefix ."foo3"), "tag2!@#$%^&*")->
store();

$obj = $bucket->get($prefix ."foo");
$links = $obj->getLinks();
Test::is(count($links), 3, 'create an object and attach a bunch of links to it');

  
$obj = $bucket->get($prefix . "foo");
$results = $obj->link("bucket")->run();
Test::is(count($results), 3, 'walk the links created');

$results = $obj->link("bucket", "tag")->run();
Test::is(count($results), 1, 'link walk bucket tag');
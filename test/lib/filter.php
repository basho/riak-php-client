<?php
$client= $new_client();
use Riak\Test;

Test::plan(2);


# Create the object...
$prefix = 'filter' . microtime(TRUE);
$bucket = $client->bucket("filter_bucket");

# Create the object...
$bucket->newObject( $prefix . "foo_one",   array("foo"=>"one"  ))->store();
$bucket->newObject($prefix . "foo_two",   array("foo"=>"two"  ))->store();
$bucket->newObject($prefix . "foo_three", array("foo"=>"three"))->store();
$bucket->newObject($prefix . "foo_four",  array("foo"=>"four" ))->store();
$bucket->newObject($prefix . "moo_five",  array("foo"=>"five" ))->store();

$mapred = $client
->add($bucket->name)
->key_filter(array('tokenize', '_', 1), array('eq', $prefix . 'foo'));
$results = $mapred->run();
Test::is(count($results), 4, 'run a filter on the key prefix');

$mapred = $client
->add($bucket->name)
->key_filter(array('starts_with', $prefix . 'foo'))
->key_filter_or(array('ends_with', $prefix . 'moo_five'));
$results = $mapred->run();
Test::is(count($results), 5, 'filter using operators starts_with and ends_with');
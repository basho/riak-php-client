<?php
$client= $new_client();
//$client->setDW(3);
use Riak\Test;

Test::plan(5);

$bucketname = 'multiBucket' . microtime(TRUE);
$bucket = $client->bucket($bucketname);
$bucket->setAllowMultiples(true);

$prefix ='sib' . time();
# Store the same object multiple times...
for ($i=0; $i<5; $i++) {
    $client= $new_client();
    $bucket = $client->bucket($bucketname);
    $obj = $bucket->newObject($prefix . 'foo', microtime(TRUE) . rand());
    $obj->store();
}


$obj = $bucket->get($prefix . 'foo');

# Make sure the object has 5 siblings...
Test::ok($obj->hasSiblings(), 'object has siblings');

Test::cmp_ok($obj->getSiblingCount(), '>', 3, 'object has more than 3 siblings');

# Test getSibling()/getSiblings()...
$siblings = $obj->getSiblings();
$obj3 = $obj->getSibling(3);
Test::is($siblings[3]->getData(), $obj3->getData(), 'data in sibling list matches up');
Test::isnt($siblings[2]->getData(), $obj3->getData(), 'different siblings dont match up'); 

# Resolve the conflict, and then do a get...
$obj2 = $obj->getSibling(2);
$obj2->store();

$obj->reload();
Test::is($obj->getData(), $obj2->getData(), 'after resolving, data matches up');

# Clean up for next test...
$obj->delete();
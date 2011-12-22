<?php
$client= $new_client();
use Riak\Test;

Test::plan(16);
$bucket = $client->bucket('testobject');
$key = $key = 't' . microtime(TRUE);
$data = array('owner'=>mt_rand(1, 1000), 'amount'=>mt_rand(1, 1000000));

Test::is( $bucket->get( $key )->exists(), FALSE, 'when i start out, the object doesnt exist in riak');
$object = $bucket->newObject($key, $data );
$object->setMeta('test', 1);
Test::ok( $object instanceof Riak\Object, 'got back an object from the bucket');
Test::is( $object->getBucket(), $bucket, 'the bucket is embedded in the object');
Test::is( $object->getKey(), $key, 'the key is stored in the object');
Test::is( $object->getData(), $data, 'the data i passed to the factory method is stored in the object');

Test::is( $object->store(), $object, 'stored the object, got back self so store method be chained');

$result = $bucket->get($key);

Test::is( $result->getData(), $data, 'loaded the object, got back the data I wrote');
Test::is( $result->getContentType(), 'text/json', 'content type is stored as json in riak');
Test::is( $result->getMeta('test'), 1, 'loaded a meta data property that i stored in the object previously');
Test::is( $result->delete(), $result, 'delete the object. chained the same way reload and store are');
Test::is( $bucket->get( $key )->exists(), FALSE, 'after delete, object no longer exists in riak');

$object = $bucket->newObject();
Test::is( $object->getKey(), NULL, 'created a new object with no key');
$object->setData( $data );
$object->store();

Test::like( $key = $object->getKey(), '#^([a-z0-9]{10,50})$#i', 'after storing the object, a key is created');
$result = $bucket->get( $key );
Test::ok( $result->exists(), 'read the object stored into riak with auto-generated key');
$result->delete();

$data = 'hello world. random value is ' . mt_rand(1, 1000000);
$key = $key = 't' . microtime(TRUE);


$object = $bucket->newBinary($key, $data);
$object->store();

$result = $bucket->getBinary( $key );
Test::is( $result->getData(), $data, 'wrote a binary value in, got it back');
$result->delete();

$key = $key = 't' . microtime(TRUE);

$bucket->newObject($key, $data = array(rand(), rand(), rand() ) )->store();

$result = $bucket->getBinary( $key );

Test::is( $result->getData(), json_encode( $data ), 'getBinary on an object stored as json returns the raw json string');

$result->delete();
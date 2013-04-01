#!/usr/bin/env php
<?php
include __DIR__ . '/../../autoload.php';
use Riak\Test;
use Riak\ProtoBuf;

Test::plan(11);

$req1 = new ProtoBuf\GetReq;
$req1->setBucket('test');
$req1->setKey('key1');
$req1->setR(2);
$req1->setPR(1);
$req1->setBasicQuorum(true);
$req1->setNotFoundOk( true );
$req1->setIfModified('etag-test1');
$req1->setHead(true);
$req1->setDeletedvclock(true);

$fp = fopen('php://memory', 'r+b');
$req1->write($fp);
rewind( $fp );

$len = $req1->size();

$req2 = new ProtoBuf\GetReq( $fp, $len ); 

Test::is( (string) $req1, (string) $req2, 're-hydrated the object from the serialized string');
rewind( $fp );
$len = $req1->size();
$data = fread( $fp, $len );
Test::is( strlen( $data ), $len, 'the serialized protobuf string written to the file pointer is the correct length');
Test::is( $req2->getBucket(), 'test', 'bucket serialized correctly');
Test::is( $req2->getKey(), 'key1', 'key name serialized correctly');
Test::is( $req2->getR(), 2, 'r serialized correctly');
Test::is( $req2->getPR(), 1, 'pr serialized correctly');
Test::is( $req2->getBasicQuorum(), true, 'basic quorum serialized correctly');
Test::is( $req2->getNotFoundOK(), true, 'not found ok flag serialized correctly');
Test::is( $req2->getIfModified(), 'etag-test1', 'if modified etag serialized correctly');
Test::is( $req2->getHead(), true, 'head flag serialized correctly');
Test::is( $req2->getDeletedVclock(), true, 'deleted vclock flag serialized correctly');
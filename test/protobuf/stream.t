#!/usr/bin/env php
<?php
include __DIR__ . '/../../autoload.php';
use Riak\Test;
use Riak\ProtoBuf;

Test::plan(2);

$fp = fopen('php://memory', 'r+b');
$stream = new ProtoBuf\Stream( $fp );
$req = new ProtoBuf\SetClientIdReq;
$req->setClientId('test12345');
$stream->write( $req );
rewind( $fp );
$result = $stream->read();
fclose( $fp );

Test::is( (string) $req, (string) $result, 'wrote the setClientIdReq and read it back off the stream');


$fp = fopen('php://memory', 'r+b');
$stream = new ProtoBuf\Stream( $fp );
$req = new ProtoBuf\GetClientIdResp;
$req->setClientId('test12345');
$stream->write( $req );
rewind( $fp );
$result = $stream->read();
fclose( $fp );

Test::is( (string) $req, (string) $result, 'wrote the GetClientIdResp and read it back off the stream');


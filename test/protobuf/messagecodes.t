#!/usr/bin/env php
<?php
include __DIR__ . '/../../autoload.php';
use Riak\Test;
use Riak\PBMessageCodes;

Test::plan(50);

$all = PBMessageCodes::getAll();

foreach( $all as $code => $name ){
    Test::is( PBMessageCodes::getCode( strtoupper( $name ) ), $code, "getCode( $name ) returned $code");
    Test::is( PBMessageCodes::getName( $code ), $name, "getName( $code ) returned $name");
}


<?php
$client= $new_client();
use Riak\Test;

Test::plan(10);

Test::is( $client->getR(), 2, 'default r value set');
$client->setR(3);
Test::is( $client->getR(), 3, 'changed r value');

Test::is( $client->getW(), 2, 'default w value set');
$client->setW(3);
Test::is( $client->getW(), 3, 'changed w value');

Test::is( $client->getDW(), 2, 'default dw value set');
$client->setDW(3);
Test::is( $client->getDW(), 3, 'changed dw value');


Test::like($client->getClientId(), '#^php_([a-z0-9]+)$#i', 'clientid starts with php and is an alphanumeric string');
$client->setClientId('test123');
Test::is( $client->getClientId(), 'test123', 'changed the client id');

Test::ok( $client->isAlive(), 'able to connect to riak and perform a ping');
Test::ok( is_array( $buckets = $client->buckets() ), 'get back a list of the buckets');


//Test::debug( $buckets );
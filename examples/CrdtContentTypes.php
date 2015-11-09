<?php

/**
 * This file was created to evaluate how content types of CRDTs are handled with the PHP client using
 * the HTTP interface of Riak
 */

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;

$key = md5(rand(0, 99) . time());

$node = (new Riak\Node\Builder)
    ->atHost('riak-test')
    ->onPort(8098)
    ->build();

$riak = new Riak([$node]);

$updateSetBuilder = (new Riak\Command\Builder\UpdateSet($riak))
    ->add('Sabres');

$updateCounterBuilder = (new Riak\Command\Builder\IncrementCounter($riak))
    ->withIncrement(1);

$command = (new Riak\Command\Builder\UpdateMap($riak))
    ->buildLocation($key, 'Teams', 'phptest_maps')
    ->updateCounter('teams', $updateCounterBuilder)
    ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
    ->withVerboseMode()
    ->build();

$response = $command->execute();
var_dump($command->getRequest());

$command = (new Riak\Command\Builder\FetchMap($riak))
    ->buildLocation($key, 'Teams', 'phptest_maps')
    ->withVerboseMode()
    ->build();

$response = $command->execute();

var_dump($response);

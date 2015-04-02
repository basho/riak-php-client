<?php

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\Node;

echo "\n========================================\n\n";

$node = (new Node\Builder)
    ->withHost('localhost')
    ->withPort(8098)
    ->build();

$riak = new Riak([$node]);

$command = (new Command\Builder\SetBucketProperties($riak))
    ->buildBucket('test')
    ->set('allow_mult', true)
    ->withVerboseMode(true)
    ->build();

$response = $command->execute($command);

echo "\n========================================\n\n";

// build an object
$command2 = (new Command\Builder\UpdateSet($riak))
    ->add('gosabres poked you.')
    ->add('phprocks viewed your profile.')
    ->add('phprocks started following you.')
    ->buildBucket('default', 'phptest_sets')
    ->withVerboseMode(true)
    ->build();

$response2 = $command2->execute($command2);
<?php

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\Node;

$node = (new Node\Builder)
    ->atHost('riak-test')
    ->onPort(8087)
    ->build();

$riak = new Riak([$node], [], new Riak\Api\Pb());

$mytable = "mytable";

$response = (new Command\Builder\TimeSeries\DescribeTable($riak))
    ->withTable($mytable)
    ->build()
    ->execute();

var_dump($response);
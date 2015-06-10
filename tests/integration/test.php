#!/usr/bin/env php
<?php
date_default_timezone_set("UTC");

require __DIR__ . '/TestSuite.php';
require __DIR__ . '/../../vendor/autoload.php';

use Integration\TestSuite;

$testSuite = new TestSuite();
$testSuite->run();
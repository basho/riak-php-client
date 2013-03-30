#!/usr/bin/env php
<?php
date_default_timezone_set("UTC");

set_include_path(
    get_include_path() .
    PATH_SEPARATOR . dirname(__DIR__) .
    PATH_SEPARATOR . dirname(__DIR__) . '/../src'
);

use integration\TestSuite;

function classLoader($className)
{
    $extensions = explode(',', spl_autoload_extensions());
    $directories = explode(PATH_SEPARATOR, get_include_path());

    foreach ($directories as $directory) {
        foreach ($extensions as $extension) {
            $classFile = $directory . DIRECTORY_SEPARATOR .
                implode(DIRECTORY_SEPARATOR, explode('\\', $className)) . $extension;

            if (file_exists($classFile) && is_readable($classFile)) {
                require_once $classFile;
                return $classFile;
            }
        }
    }
}

spl_autoload_extensions('.php');
spl_autoload_register('classLoader');

$testSuite = new TestSuite();
$testSuite->run();
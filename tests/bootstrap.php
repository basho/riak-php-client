<?php

error_reporting(E_ALL | E_STRICT);

if ( ! $autoloader = @include __DIR__ . '/../vendor/autoload.php') {
    die("You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev
");
}

use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);
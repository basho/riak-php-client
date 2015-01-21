# Riak Client for PHP [![Build Status](https://secure.travis-ci.org/basho/riak-php-client.png?branch=master)](http://travis-ci.org/basho/riak-php-client)

A PHP client/toolkit for [Riak](http://basho.com/riak/), Basho's distributed database. This client library is officially supported and
maintained by the Basho engineering team.

**NOTICE: THE 2.0.X BRANCH IS UNDER ACTIVE DEVELOPMENT AND SHOULD NOT BE USED IN PRODUCTION AT THIS TIME**

## Releases

The release tags of this project have been aligned with the major & minor release versions of Riak. For example, if you
are using version 2.0.1 of Riak, then you will want the latest 2.0.* version of this library.

## Dependencies

**Release 2.x.x**
* PHP 5.4+.


## Installation

### Composer Install (recommended)

Run the following `composer` command:

```console
$ composer require "basho/riak-php-client:2.0.*"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "basho/riak-php-client": "2.0.*"
}
```

And then run `composer update` to ensure the module is installed.

### Manual Install

**TODO**

## Documentation
API documentation for this library can be found on [Github Pages](http://basho.github.com/riak-php-client)

To see other clients available for use with Riak visit our
[Documentation Site](http://docs.basho.com/riak/latest/dev/using/libraries)

## Overview

Version 2.0 of the Riak PHP client is a completely new codebase.

## Getting started with the 2.0 client.

The easiest way to get started with the client is using a `RiakClientBuilder` :

```php
$builder = new RiakClientBuilder();
$client  = $builder
    ->withNodeUri('http://192.168.1.1:8098')
    ->withNodeUri('http://192.168.1.2:8098')
    ->withNodeUri('http://192.168.1.3:8098')
    ->build();

```

Once you have a $client, commands from the `Basho\Riak\Command*` namespace are built then executed by the client.

Some basic examples of building and executing these commands is shown
below.

## Getting Data In

```php
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Command\Kv\StoreValue;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;

$object    = new RiakObject();
$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

$object->setValue('[1,1,1]');
$object->setContentType('application/json');

// store object
$store    = StoreValue::builder($location, $object)
    ->withOption(RiakOption::PW, 1)
    ->withOption(RiakOption::W, 2)
    ->build();

$client->execute($store);
```

## Getting Data Out

```php
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Command\Kv\FetchValue;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;

$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

// fetch object
$fetch  = FetchValue::builder($location)
    ->withOption(RiakOption::NOTFOUND_OK, true)
    ->withOption(RiakOption::R, 1)
    ->build();

$result = $client->execute($fetch);
$object = $result->getValue();
```

## Removing Data

```php
use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Command\Kv\DeleteValue;
use Basho\Riak\Core\Query\RiakObject;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;

$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

// delete object
$delete  = DeleteValue::builder($location)
    ->withOption(RiakOption::PW, 1)
    ->withOption(RiakOption::W, 2)
    ->build();

$this->client->execute($delete);
```

## Merging siblings

Siblings are an important feature in Riak,
for this purpose we have the interface `Basho\Riak\Resolver\ConflictResolver`,
you implement this interface to resolve  siblings into a single object.

Below is a simple example that will just merge the content of siblings :
```php
use \Basho\Riak\Resolver\ConflictResolver;
use \Basho\Riak\Core\Query\RiakObject;
use \Basho\Riak\Core\Query\RiakList;

class MySimpleResolver implements ConflictResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(RiakList $siblings)
    {
        $result  = new MyDomainObject();
        $content = "";

        /** @var $object \MyDomainObject */
        foreach ($siblings as $object) {
            $content .= $object->getValue();
        }

        $result->setValue($content);

        return $result;
    }
}

// register your resolver during the application start up
/** @var $builder \Basho\Riak\RiakClientBuilder */
$client = $builder
    ->withConflictResolver('MyDomainObject' new MySimpleResolver())
    ->withNodeUri('http://localhost:8098')
    ->build();

// fetch the object and resolve any possible conflict
$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');
$fetch     = FetchValue::builder($location)
    ->withOption(RiakOption::NOTFOUND_OK, true)
    ->withOption(RiakOption::R, 1)
    ->build();

/** @var $domain \MySimpleResolver */
$result = $client->execute($fetch);
$domain = $result->getValue('MyDomainObject');
```

## Contributing

This is an open source project licensed under the Apache 2.0 License. We encourage and welcome contributions to the
project from the community.

### Coding Standards

Here are the standards we expect to see when considering pull requests

* [PSR-2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PHP / Pear Docblock Guide](http://pear.php.net/manual/en/standards.sample.php)
* [PHPUnit Tests](https://phpunit.de/manual/current/en/phpunit-book.html)

### Docblock Comments

Complete docblock comments that follow the Pear / PHP standard not only assist in generating documentation, but also 
development, as IDE's like PHPStorm, ZendStudio, NetBeans, etc use the information found in the comments to improve the
development experience. It is expected that any new code in the library is committed with complete docblock comments.
This includes:

* Short & long descriptions for all new classes and all class members, properties and constants.
* @author, @copyright, @license, and @since need to be included on all new classes.
* @var tag on every class property
* A @param tag for every parameter on a class method
* A @return tag for every class method that returns a value

Here is an example of a class docblock:
```php
/**
 * Class Riak
 *
 * A more elaborate description of what this class does. It may include warnings, limitations or examples.
 *
 * @author      Author Name <author@domain.com>
 * @copyright   2011-2015 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
```

### Unit & Integration Tests

We want to ensure that all code that is included in a release has proper coverage with unit tests. It is expected that
all pull requests that include new classes or class methods have appropriate unit tests included with the PR.

**TODO: Finish writing up the unit test section, provide examples**

#### Running Tests

We also expect that before submitting a pull request, that you have run the tests to ensure that all of them
continue to pass after your changes.

To run the tests, clone this repository and run `composer update` from the repository root, then you can execute all the tests by simply running
`php vendor/bin/phpunit`.

* To execute tests, run `./vendor/bin/phpunit`
* To check code standards, run `./vendor/bin/phpcs -p --extensions=php  --standard=ruleset.xml src`

# Riak Client for PHP [![Build Status](https://secure.travis-ci.org/basho/riak-php-client.png?branch=master)](http://travis-ci.org/basho/riak-php-client)

A PHP client/toolkit for [Riak](http://basho.com/riak/), Basho's distributed database. This client library is officially supported and
maintained by the Basho engineering team.

**NOTICE: THE 2.0.X BRANCH IS UNDER ACTIVE DEVELOPMENT AND SHOULD NOT BE USED IN PRODUCTION AT THIS TIME**

## Releases

The release tags of this project have been aligned with the major & minor release versions of Riak. For example, if you
are using version 1.4.9 of Riak, then you will want the latest 1.4.* version of this library.

## Dependencies

**Release 2.x.x**
* PHP 5.4+.
* ext-curl

**Release 1.4.x**
* PHP 5.3+.
* ext-curl


## Installation

### Composer Install (recommended)

Run the following `composer` command:

```console
$ composer require "basho/riak-php-client:1.4.*"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "basho/riak-php-client": "1.4.*"
}
```

And then run `composer update` to ensure the module is installed.

### Manual Install

**TODO**

## Documentation
API documentation for this library can be found on [Github Pages](http://basho.github.com/riak-php-client)

To see other clients available for use with Riak visit our
[Documentation Site](http://docs.basho.com/riak/latest/dev/using/libraries)

## Examples

```php
use Basho\Riak;
use Basho\Riak\Node\Builder as NodeBuilder;
use Basho\Riak\Command\Builder as CommandBuilder;
use Basho\Riak\Command\Object\Store;
use Basho\Riak\Object;

$nodes = (new NodeBuilder)
    ->withPort(10018)
    ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',]);

$riak = new Riak(static::$nodes);

$object = new Object('test_key');
$object->setData('test_data');

$command = (new CommandBuilder(new Store()))
    ->withObject($object)
    ->build();
    
$result = $riak->execute($command);
```

## Contributing

This is an open source project licensed under the Apache 2.0 License. We encourage and welcome contributions to the
project from the community. We ask that you keep in mind these considerations when planning your contribution.

* Regardless whether your contribution is for a bug fix or a feature request, create an Issue in GitHub and let us know
what you are thinking.
* For bugs, if you have already found a fix, feel free to submit a Pull Request referencing the Issue you created.
* For feature requests, we want to improve upon the library incrementally which means small changes at a time. In order
ensure your PR can be reviewed in a timely manner, please keep PRs small, e.g. <10 files and <500 lines changed. If you
think this is unrealistic, then mention that within the GH Issue and we can discuss it.
* Before you open the PR, please review the following regarding Coding Standards, Docblock comments and 
unit / integration tests to reduce delays in getting your changes approved.

### Coding Standards

Here are the standards we expect to see when considering pull requests

* [PSR-2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PHP / Pear Docblock Guide](http://pear.php.net/manual/en/standards.sample.php)
* [PHPUnit Tests](https://phpunit.de/manual/current/en/phpunit-book.html)
* Please suffix all Interfaces and Traits with the descriptors Interface and Trait, e.g. ObjectInterface & ObjectTrait

### Docblock Comments

Complete docblock comments that follow the Pear / PHP standard not only assist in generating documentation, but also 
development, as IDE's like PHPStorm, ZendStudio, NetBeans, etc use the information found in the comments to improve the
development experience. It is expected that any new code in the library is committed with complete docblock comments.
This includes:

* Short & long descriptions for all new classes and all class members, properties and constants.
* @package, @author, @copyright, @license, and @since need to be included on all new classes.
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
 * @package     Basho\Riak
 * @author      Author Name <author@domain.com>
 * @copyright   2011-2014 Basho Technologies, Inc.
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

To execute only the unit tests, run `php vendor/bin/phpunit --testsuite 'Unit Tests'`.

To execute only the integration tests, run `php vendor/bin/phpunit --testsuite 'Integration Tests'`.

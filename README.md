# Riak Client for PHP

**Riak PHP Client** is a client which makes it easy to communicate with [Riak](http://basho.com/riak/), an open source, distributed database that focuses on high availability, horizontal scalability, and *predictable*
latency. Both Riak and this code is maintained by [Basho](http://www.basho.com/). 

To see other clients available for use with Riak visit our
[Documentation Site](http://docs.basho.com/riak/latest/dev/using/libraries)


1. [Installation](#installation)
2. [Documentation](#documentation)
3. [Contributing](#contributing)
	* [An honest disclaimer](#an-honest-disclaimer)
4. [Roadmap](#roadmap)
5. [License and Authors](#license-and-authors)


## Installation *TODO UPDATE*

### Dependencies
* **Release 2.x.x** requires PHP 5.4+
* **Release 1.4.x** requires PHP 5.3+

### Composer Install
Run the following `composer` command:

```console
$ composer require "basho/riak": "2.0.*"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "basho/riak": "2.0.*"
}
```

And then run `composer update` to ensure the module is installed.

## Documentation
* Master: [![Build Status](https://secure.travis-ci.org/basho/riak-php-client.png?branch=master)](http://travis-ci.org/basho/riak-php-client)

A fully traversable version of the API documentation for this library can be found on [Github Pages](http://basho.github.io/riak-php-client). 

### Releases
The release tags of this project have been aligned with the major & minor release versions of Riak. For example, if you are using version 1.4.9 of Riak, then you will want the latest 1.4.* version of this library.

### Example Usage *TODO UPDATE*
Below is a short example of using the client. More substantial sample code is available [in examples](/examples).
```php
// lib classes are included via the Composer autoloader files
use Basho\Riak;
use Basho\Riak\Node;
use Basho\Riak\Command;

// define the connection info to our Riak nodes
$nodes = (new Node\Builder)
    ->onPort(10018)
    ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',]);

// instantiate the Riak client
$riak = new Riak($nodes);

// build a command to be executed against Riak
$command = (new Command\Builder\StoreObject($riak))
    ->buildObject('some_data')
    ->buildBucket('users')
    ->build();
    
// Receive a response object
$response = $command->execute($command);

// Retrieve the Location of our newly stored object from the Response object
$object_location = $response->getLocation();
```

## Contributing
This repo's maintainers are engineers at Basho and we welcome your contribution to the project! You can start by reviewing [CONTRIBUTING.md](CONTRIBUTING.md) for information on everything from testing to coding standards.

### An honest disclaimer

Due to our obsession with stability and our rich ecosystem of users, community updates on this repo may take a little longer to review. 

The most helpful way to contribute is by reporting your experience through issues. Issues may not be updated while we review internally, but they're still incredibly appreciated.

Thank you for being part of the community! We love you for it. 

## Roadmap
* Current develop & master branches contain feature support for Riak version 2.0
* Development for Riak 2.1 features is underway and expected to be completed during Q2 2015

## License and Authors

* Author: Christopher Mancini (https://github.com/christophermancini)
* Author: Alex Moore (https://github.com/alexmoore)

Copyright (c) 2015 Basho Technologies, Inc. Licensed under the Apache License, Version 2.0 (the "License"). For more details, see [License](License).
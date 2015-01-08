<img src="http://docs.basho.com/shared/1.2.1/images/riak-logo.png">

# Riak PHP Client #
This is the official PHP client for Riak.

[![Build Status](https://secure.travis-ci.org/basho/riak-php-client.png?branch=master)](http://travis-ci.org/basho/riak-php-client)

## Documentation ##
API documentation for this library can be found at<br/>
<http://basho.github.com/riak-php-client/>

(See **Documentation Maintenance** at the bottom of the README for instructions on updating the docs.)

Documentation for use of Riak clients in general can be found at<br/>
<http://docs.basho.com/riak/latest/references/Client-Libraries/>

## Repositories ##

The official source code for this client can be retrieved from<br/>
<http://github.com/basho/riak-php-client/>

Riak can be obtained pre-built from<br/>
<http://basho.com/resources/downloads/>

or as source from<br/>
<http://github.com/basho/riak/>

## Installation ##
  ```bash
  composer require basho/riak
  ```

This quick example assumes that you have a local riak cluster running on port 8098

```php

use Basho\Riak\Cap\RiakOption;
use Basho\Riak\Command\Kv\FetchValue;
use Basho\Riak\Command\Kv\StoreValue;
use Basho\Riak\Command\Kv\DeleteValue;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\Query\RiakNamespace;
use Basho\Riak\RiakClientBuilder;

$builder = new RiakClientBuilder();
$client  = $builder
    ->withNodeUri('http://localhost:8098')
    ->build();

$object   = new SimpleObject('[1,1,1]');
$location = new RiakLocation(new RiakNamespace('bucket_name', 'bucket_type'), $key);

// store object
$store    = StoreValue::builder($location, $object1)
    ->withOption(RiakOption::PW, 1)
    ->withOption(RiakOption::W, 2)
    ->build();

$this->client->execute($store);

// fetch object
$fetch  = FetchValue::builder($location)
    ->withOption(RiakOption::NOTFOUND_OK, true)
    ->withOption(RiakOption::R, 1)
    ->build();

$fetchResult = $this->client->execute($fetch);
$siblings    = $fetchResult->getValues();

// delete object
$delete  = DeleteValue::builder($location)
    ->withOption(RiakOption::PW, 1)
    ->withOption(RiakOption::W, 2)
    ->build();

$this->client->execute($delete);

```
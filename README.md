[![Build Status](https://secure.travis-ci.org/localgod/riak-php-client.png?branch=master)](http://travis-ci.org/localgod/riak-php-client)

<img src="http://docs.basho.com/shared/1.2.1/images/riak-logo.png">

# Riak PHP Client #

## Changes from official client ##
This is an updated version of the official PHP client for Riak. 
* This version of the client use namespaces which means your PHP version should be >= 5.3.0
* The "Riak" prefix has been removed from all classes.
* It is PSR-0, PSR-1 and PSR-2 complient (https://github.com/php-fig/fig-standards).
* It can be used with Composer (http://getcomposer.org/).
* Tests are rewritten for the PHPUnit framework (https://github.com/sebastianbergmann/phpunit/)
* The methods key_filter(), key_filter_and(), key_filter_or() and key_filter_operator() on the [MapReduce](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.MapReduce.html) class have been renamed to keyFilter(), keyFilterAnd(), keyFilterOr() and keyFilterOperator() respectivly.
* The method to_array on the [Link\Phase](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Link.Phase.html) and the [MapReduce\Phase](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.MapReduce.Phase.html) classes have been renamed to toArray()
* You can no longer use the get* methods like [getDW()](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Bucket.html#method_getDW) to set object properties on the bucket object. Use the set* methods.
* Bucket properties has been moved to the [Properties](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Properties.html) class.
* [Bucket::getProperties()](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Bucket.html#method_getProperties) now return a Properties object.
* All properties related methods on Bucket has been moved to the Properties class.


## Documentation ##
API documentation for this library can be found at<br/>
<http://localgod.github.com/riak-php-client/>

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
Clone this repository to fetch the latest version of this client
```bash
git clone git://github.com/localgod/riak-php-client.git
```
## Quick start ##
PHP should be configured with curl enabled
```bash
./configure --with-curl
```
Confirm your PHP installation has curl enabled
```bash
php -m | grep curl
```
This quick example assumes that you have a local riak cluster running on port 8098
```php
use Basho\Riak\Bucket, Basho\Riak\Client;
# Connect to Riak
$client = new Client('127.0.0.1', 8098);

# Choose a bucket name
$bucket = $client->bucket('test');

# Supply a key under which to store your data
$person = $bucket->newObject('riak_developer_1', array(
    'name' => "John Smith",
    'age' => 28,
    'company' => "Facebook"
));

# Save the object to Riak
$person->store();

# Fetch the object
$person = $bucket->get('riak_developer_1');

# Update the object
$person->data['company'] = "Google";
$person->store();
```
## Connecting ##
Connect to a Riak server by specifying the address or hostname and port:
```php
# Connect to Riak
$client = new Client('127.0.0.1', 8098);
```
This method returns a [Client](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Client.html)

## Using Buckets ##
To select a bucket, use the Client::bucket() method
```php
# Choose a bucket name
$bucket = $client->bucket('test');
```
or using the Bucket() constructor
```php
# Create a bucket
$bucket = new Bucket($client, 'test');
```
If a bucket by this name does not already exist, a new one will be created for you when you store your first key.
This method returns a [Bucket](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Bucket.html)

## Creating Objects ##
Objects can be created using the Bucket::newObject() method
```php
# Create an object for future storage and populate it with some data
$person = $bucket->newObject('riak_developer_1');
```
or using the Object() constructor
```php
# Create an object for future storage
$person = new Object($client, $bucket, 'riak_developer_1');
```
Both methods return a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Setting Object Values ##
Object data can be set using the Object::setData() method
```php
# Populate object with some data
$person->setData(array(
    'name' => "John Smith",
    'age' => 28,
    'company' => "Facebook"
));
```
or you may modify the object's data property directly (not recommended)
```php
# Populate object with some data
$person->data = array(
    'name' => "John Smith",
    'age' => 28,
    'company' => "Facebook"
);
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Storing Objects ##
Objects can be stored using the Object::store() method
```php
# Save the object to Riak
$person->store();
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Chaining ##
For methods like newObject(), setData() and store() which return objects of a similar class (in this case Object), chaining can be used to perform multiple operations in a single statement.
```php
# Create, set, and store an object
$data = array(
	'name' => "John Smith",
	'age' => 28,
	'company' => "Facebook"
);
$bucket->newObject('riak_developer_1')->setData($data)->store();
```
## Fetching Objects ##
Objects can be retrieved from a bucket using the Bucket::get() method
```php
# Save the object to Riak
$person = $bucket->get('riak_developer_1');
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Modifying Objects ##
Objects can be modified using the Object::store() method
```php
# Update the object
$person = $bucket->get('riak_developer_1');
$person->data['company'] = "Google";
$person->store();
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Deleting Objects ##
Objects can be deleted using the Object::delete() method
```php
# Update the object
$person = $bucket->get('riak_developer_1');
$person->delete();
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Adding a Link ##
Links can be added using RiakObject::addLink()
```php
# Add a link from John to Dave
$john = $bucket->get('riak_developer_1');
$dave = $bucket->get('riak_developer_2');
$john->addLink($dave, 'friend')->store();
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Removing a Link ##
Links can be removed using Object::removeLink()
```php
# Remove the link from John to Dave
$john = $bucket->get('riak_developer_1');
$dave = $bucket->get('riak_developer_2');
$john->removeLink($dave, 'friend')->store();
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Retrieving Links ##
An object's links can be retrieved using Object::getLinks()
```php
# Retrieve all of John's links
$john = $bucket->get('riak_developer_1');
$links = $john->getLinks();
```
This method returns an array of [Link](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Link.html)s

## Linkwalking ##
Linkwalking can be done using the Object::link() method
```php
# Retrieve all of John's friends
$john = $bucket->get('riak_developer_1');
$friends = $john->link($bucket->name, "friend")->run();
```
This method returns an array of [Link](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Link.html)s

## Dereferencing Links ##
RiakLinks can be dereferenced to the linked object using the RiakLink::get() method
```php
# Retrieve all of John's friends
$john = $bucket->get('riak_developer_1');
$dave = $bucket->get('riak_developer_2');
$john->addLink($dave, 'friend')->store();
$friends = $john->link($bucket->name, "friend")->run();
$dave = $friends[0]->get();
```
This method returns a [Object](http://localgod.github.com/riak-php-client/api/classes/Basho.Riak.Object.html)

## Fetching Data With Map/Reduce ##
Data can be fetched by Map and Reduce using the Client::add() method
```php
# Fetch a sorted list of all keys in a bucket
$result = $client->add($bucket->name)
	->map("function (v) { return [v.key]; }")
	->reduce("Riak.reduceSort")
	->run();
```
This method returns an array of data representing the result of the Map/Reduce functions.

## Using Key Filters With Map/Reduce ##
When using Map/Reduce on a bucket, you can use key filters to determine the applicable key set using the MapReduce::keyFilter(), MapReduce::keyFilterAnd(), and MapReduce::keyFilterOr() methods.
```php
# Retrieve the keys of all invoices from May 30, 2010
$result = $client->add($bucket->name)
    ->keyFilter(array('tokenize', '.', 1), array('eq', 'invoice'))
    ->keyFilterAnd(array('tokenize', '.', 2), array('ends_with', '20100530'))
    ->map("function (v) { return [v.key]; }")
    ->reduce("Riak.reduceSort")
    ->run();
```
This method returns an array of data representing the result of the Map/Reduce functions.

## Using Search ##
Searches can be executed using the Client::search() method
```php
# Create some test data
$bucket = $client->bucket("searchbucket");
$bucket->newObject("one", array("foo"=>"one", "bar"=>"red"))->store();
$bucket->newObject("two", array("foo"=>"two", "bar"=>"green"))->store();

# Execute a search for all objects with matching properties
$results = $client->search("searchbucket", "foo:one OR foo:two")->run();
```
This method will return null unless executed against a Riak Search cluster.

## Meta Data ##
You can provide meta data on objects using Object::getMeta() and Object::setMeta()
```php
# Set some new meta data
$object->setMeta("some-meta", "some-value");
    
# Get some meta data (returns null if not found)
$object->getMeta("some-meta");
    
# Get all meta data (an array keyed by meta name)
$object->getAllMeta()
```
Remove existing metadata
```php
# Remove a single value
$object->removeMeta("some-meta");
    
# Remove all meta data
$object->removeAllMeta();
```
## Secondary Indexes ##

### Adding Secondary Indexes ###
Secondary indexes can be added using the Object::addIndex() and Object::addAutoIndex() methods.  

Auto indexes are kept fresh with the associated field automatically, so if you read an object, modify its data, and write it back, the auto index will reflect the new value from the object.  Traditional indexes are fixed and must be manually managed.  *NOTE* that auto indexes are a function of the Riak PHP client, and are not part of native Riak functionality.  Other clients writing the same object must manage the index manually.
```php
# Create some test data
$bucket = $client->bucket("indextest");
$bucket
  ->newObject("one", array("some_field"=>1, "bar"=>"red"))
  ->addIndex("index_name", "int", 1)
  ->addIndex("index_name", "int", 2)
  ->addIndex("text_index", "bin", "apple")
  ->addAutoIndex("some_field", "int")
  ->addAutoIndex("bar", "bin")
  ->store();
```
You can remove a specific value from an index, all values from an index, or all indexes:
```php
# Remove just a single value
$object->removeIndex("index_name", "int", 2);
    
# Remove all values from an index
$object->removeAllIndexes("index_name", "int");
    
# Remove all index types for a given index name
$object->removeAllIndexes("index_name");
    
# Remove all indexes
$object->removeAllIndexes();
```
Likewise you can remove auto indexes:
```php
# Just the "foo" index
$object->removeAutoIndex("foo", "int");
    
# All auto indexes
$object->removeAllAutoIndexes("foo", "int");
    
# All auto indexes
$object->removeAllAutoIndexes();
```
Mass load indexes, or just replace an existing index:
```php
$object->setIndex("index_name", "int", array(1, 2, 3));
$object->setIndex("text_index", "bin", "foo");
```
### Querying a Bucket's secondary index ###
Secondary indexes can be queried using the Bucket::indexSearch() method.  This returns an array of Link objects.
```php
# Exact Match
$results = $bucket->indexSearch("index_name", "int", 1);
foreach ($results as $link) {
    echo "Key: {$link->getKey()}<br/>";
    $object = $link->get();
}

# Range Search
$results = $bucket->indexSearch("index_name", "int", 1, 10);
```
Duplicate entries may be found in a ranged index search if a given index has multiple values that fall within the range.  You can request that these duplicates be eliminated in the result.
```php
$results = $bucket->indexSearch("index_name", "int", 1, 10, true);
```
### Secondary Indexes in Map/Reduce ###
The same search format used for Bucket::indexSearch() may be used during Map/Reduce operations during the input phase.  This is only valid for bucket-level operations, and cannot be combined with other filtration methods such as key filters.
```php
# Use secondary indexes to speed up our Map/Reduce operation
$result = $client
    ->add("bucket_name") // Begin Map/Reduce
    ->indexSearch("index_name", "int", 1)
    ->map("function (v) { return [v.key]; }")
    ->reduce("Riak.reduceSort")
    ->run();
```

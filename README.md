<img src="http://www.basho.com/images/riaklogo.png">

# Riak PHP Client #
This is the official PHP client for Riak.

## Documentation ##
Documentation for this PHP library can be found in this repository's docs subdirectory.
http://basho.github.com/riak-php-client/

Documentation for use of Riak clients in general can be found at
https://wiki.basho.com/display/RIAK/Client+Libraries

## Repositories ##

The official source code for this client can be retrieved from
http://github.com/basho/riak-php-client/

Riak can be obtained pre-built from
http://downloads.basho.com/riak/

or as source from
http://github.com/basho/riak/

## Installation ##
Clone this repository to fetch the latest version of this client 

    git clone git://github.com/basho/riak-php-client.git

## Quick start ##
This quick example assumes that you have a local riak cluster running on port 8098

    require_once('riak-php-client/riak.php');

    # Connect to Riak
    $client = new RiakClient('127.0.0.1', 8098);

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

## Connecting ##
Connect to a Riak server by specifying the address or hostname and port:
    
    # Connect to Riak
    $client = new RiakClient('127.0.0.1', 8098);
    
This method returns a [RiakClient](http://basho.github.com/riak-php-client/class_riak_client.html)

## Using Buckets ##
To select a bucket, use the RiakClient::bucket() method

    # Choose a bucket name
    $bucket = $client->bucket('test');

or using the RiakBucket() constructor

    # Create a bucket
    $bucket = new RiakBucket($client, 'test');

If a bucket by this name does not already exist, a new one will be created for you when you store your first key.
This method returns a [RiakBucket](http://basho.github.com/riak-php-client/class_riak_bucket.html)

## Creating Objects ##
Objects can be created using the RiakBucket::newObject() method

    # Create an object for future storage and populate it with some data
    $person = $bucket->newObject('riak_developer_1');

or using the RiakObject() constructor

    # Create an object for future storage
    $person = new RiakObject($client, $bucket, 'riak_developer_1');

Both methods return a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Setting Object Values ##
Object data can be set using the RiakObject::setData() method

    # Populate object with some data
    $person->setData(array(
        'name' => "John Smith",
        'age' => 28,
        'company' => "Facebook"
    ));

or you may modify the object's data property directly (not recommended)

    # Populate object with some data
    $person->data = array(
        'name' => "John Smith",
        'age' => 28,
        'company' => "Facebook"
    );

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Storing Objects ##
Objects can be stored using the RiakObject::store() method

    # Save the object to Riak
    $person->store();

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Chaining ##
For methods like newObject(), setData() and store() which return objects of a similar class (in this case RiakObject), chaining can be used to perform multiple operations in a single statement.

    # Create, set, and store an object
    $data = array(
    	'name' => "John Smith",
    	'age' => 28,
    	'company' => "Facebook"
    );
    $bucket->newObject('riak_developer_1')->setData($data)->store();

## Fetching Objects ##
Objects can be retrieved from a bucket using the RiakBucket::get() method

    # Save the object to Riak
    $person = $bucket->get('riak_developer_1');

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Modifying Objects ##
Objects can be modified using the RiakObject::store() method

    # Update the object
    $person = $bucket->get('riak_developer_1');
    $person->data['company'] = "Google";
    $person->store();

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Deleting Objects ##
Objects can be deleted using the RiakObject::delete() method

    # Update the object
    $person = $bucket->get('riak_developer_1');
    $person->delete();

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Adding a Link ##
Links can be added using RiakObject::addLink()

    # Add a link from John to Dave
    $john = $bucket->get('riak_developer_1');
    $dave = $bucket->get('riak_developer_2');
    $john->addLink($dave, 'friend')->store();

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Removing a Link ##
Links can be removed using RiakObject::removeLink()

    # Remove the link from John to Dave
    $john = $bucket->get('riak_developer_1');
    $dave = $bucket->get('riak_developer_2');
    $john->removeLink($dave, 'friend')->store();

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Retrieving Links ##
An object's links can be retrieved using RiakObject::getLinks()

    # Retrieve all of John's links
    $john = $bucket->get('riak_developer_1');
    $links = $john->getLinks();

This method returns an array of [RiakLink](http://basho.github.com/riak-php-client/class_riak_link.html)s

## Linkwalking ##
Linkwalking can be done using the RiakObject::link() method

    # Retrieve all of John's friends
    $john = $bucket->get('riak_developer_1');
    $friends = $john->link($bucket->name, "friend")->run();
    
This method returns an array of [RiakLink](http://basho.github.com/riak-php-client/class_riak_link.html)s

## Dereferencing Links ##
RiakLinks can be dereferenced to the linked object using the RiakLink::get() method

    # Retrieve all of John's friends
    $john = $bucket->get('riak_developer_1');
    $dave = $bucket->get('riak_developer_2');
    $john->addLink($dave, 'friend')->store();
    $friends = $john->link($bucket->name, "friend")->run();
    $dave = $friends[0]->get();

This method returns a [RiakObject](http://basho.github.com/riak-php-client/class_riak_object.html)

## Fetching Data With Map/Reduce ##
Data can be fetched by Map and Reduce using the RiakClient::add() method

    # Fetch a sorted list of all keys in a bucket
    $result = $client->add($bucket->name)
    	->map("function (v) { return [v.key]; }")
    	->reduce("Riak.reduceSort")
    	->run();

This method returns an array of data representing the result of the Map/Reduce functions.

*More examples of Map/Reduce can be found in unit_tests.php*

## Using Search ##
Searches can be executed using the RiakClient::search() method

    # Create some test data
    $bucket = $client->bucket("searchbucket");
    $bucket->newObject("one", array("foo"=>"one", "bar"=>"red"))->store();
    $bucket->newObject("two", array("foo"=>"two", "bar"=>"green"))->store();
    
    # Execute a search for all objects with matching properties
    $results = $client->search("searchbucket", "foo:one OR foo:two")->run();

This method will return null unless executed against a Riak Search cluster.

## Additional Resources ##

See unit_tests.php for more examples.
https://github.com/PharkMillups/riak-php-client/blob/master/unit_tests.php
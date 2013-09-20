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
Clone this repository to fetch the latest version of this client

    git clone git://github.com/basho/riak-php-client.git

## Quick start ##
PHP should be configured with curl enabled

    ./configure --with-curl

Confirm your PHP installation has curl enabled

    php -m | grep curl

This quick example assumes that you have a local riak cluster running on port 8098

    require_once('riak-php-client/src/Basho/Riak/Riak.php');
    require_once('riak-php-client/src/Basho/Riak/Bucket.php');
    require_once('riak-php-client/src/Basho/Riak/Exception.php');
    require_once('riak-php-client/src/Basho/Riak/Link.php');
    require_once('riak-php-client/src/Basho/Riak/MapReduce.php');
    require_once('riak-php-client/src/Basho/Riak/Object.php');
    require_once('riak-php-client/src/Basho/Riak/StringIO.php');
    require_once('riak-php-client/src/Basho/Riak/Utils.php');
    require_once('riak-php-client/src/Basho/Riak/Link/Phase.php');
    require_once('riak-php-client/src/Basho/Riak/MapReduce/Phase.php');

    # Connect to Riak
    $client = new Basho\Riak\Riak('127.0.0.1', 8098);

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
    $client = Basho\Riak\Riak('127.0.0.1', 8098);

This method returns a [Riak](http://basho.github.io/riak-php-client/classes/Basho.Riak.Riak.html) object.

## Using Buckets ##
To select a bucket, use the Riak::bucket() method

    # Choose a bucket name
    $bucket = $client->bucket('test');

or using the Basho\Riak\Bucket() constructor

    # Create a bucket
    $bucket = new Basho\Riak\Bucket($client, 'test');

If a bucket by this name does not already exist, a new one will be created for you when you store your first key.
This method returns a [Bucket](http://basho.github.io/riak-php-client/classes/Basho.Riak.Bucket.html)

## Creating Objects ##
Objects can be created using the Basho\Riak\Bucket::newObject() method

    # Create an object for future storage and populate it with some data
    $person = $bucket->newObject('riak_developer_1');

or using the Basho\Riak\Object() constructor

    # Create an object for future storage
    $person = new Basho\Riak\Object($client, $bucket, 'riak_developer_1');

Both methods return an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Setting Object Values ##
Object data can be set using the Object::setData() method

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

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Storing Objects ##
Objects can be stored using the Object::store() method

    # Save the object to Riak
    $person->store();

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Chaining ##
For methods like newObject(), setData() and store() which return objects of a similar class (in this case Object), chaining can be used to perform multiple operations in a single statement.

    # Create, set, and store an object
    $data = array(
    	'name' => "John Smith",
    	'age' => 28,
    	'company' => "Facebook"
    );
    $bucket->newObject('riak_developer_1')->setData($data)->store();

or

    # Another way to create, set, and store an object
    $data = array(
    	'name' => "John Smith",
    	'age' => 28,
    	'company' => "Facebook"
    );
    $bucket->newObject('riak_developer_1',$data)->store();

## Fetching Objects ##
Objects can be retrieved from a bucket using the Bucket::get() method

    # Retrieve the object from a bucket
    $person = $bucket->get('riak_developer_1');

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Modifying Objects ##
Objects can be modified using the Object::store() method

    # Update the object
    $person = $bucket->get('riak_developer_1');
    $person->data['company'] = "Google";
    $person->store();

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Deleting Objects ##
Objects can be deleted using the Object::delete() method

    # Delete the object
    $person = $bucket->get('riak_developer_1');
    $person->delete();

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Adding a Link ##
Links can be added using Object::addLink()

    # Add a link from John to Dave
    $john = $bucket->get('riak_developer_1');
    $dave = $bucket->get('riak_developer_2');
    $john->addLink($dave, 'friend')->store();

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Removing a Link ##
Links can be removed using Object::removeLink()

    # Remove the link from John to Dave
    $john = $bucket->get('riak_developer_1');
    $dave = $bucket->get('riak_developer_2');
    $john->removeLink($dave, 'friend')->store();

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Retrieving Links ##
An object's links can be retrieved using Object::getLinks()

    # Retrieve all of John's links
    $john = $bucket->get('riak_developer_1');
    $links = $john->getLinks();

This method returns an array of [Link](http://basho.github.io/riak-php-client/classes/Basho.Riak.Link.html)s

## Linkwalking ##
Linkwalking can be done using the Object::link() method

    # Retrieve all of John's friends
    $john = $bucket->get('riak_developer_1');
    $friends = $john->link($bucket->name, "friend")->run();

This method returns an array of [Link](http://basho.github.io/riak-php-client/classes/Basho.Riak.Link.html)s

## Dereferencing Links ##
Links can be dereferenced to the linked object using the Link::get() method

    # Retrieve all of John's friends
    $john = $bucket->get('riak_developer_1');
    $dave = $bucket->get('riak_developer_2');
    $john->addLink($dave, 'friend')->store();
    $friends = $john->link($bucket->name, "friend")->run();
    $dave = $friends[0]->get();

This method returns an [Object](http://basho.github.io/riak-php-client/classes/Basho.Riak.Object.html)

## Fetching Data With Map/Reduce ##
Data can be fetched by Map and Reduce using the Riak::add() method

    # Fetch a sorted list of all keys in a bucket
    $result = $client->add($bucket->name)
    	->map("function (v) { return [v.key]; }")
    	->reduce("Riak.reduceSort")
    	->run();

This method returns an array of data representing the result of the Map/Reduce functions.

*More examples of Map/Reduce can be found in TestSuite.php <br/>
<https://github.com/basho/riak-php-client/blob/master/tests/integration/TestSuite.php>*

## Using Key Filters With Map/Reduce ##
When using Map/Reduce on a bucket, you can use key filters to determine the applicable key set using the MapReduce::key_filter(), MapReduce::key_filter_and(), and MapReduce::key_filter_or() methods.

    # Retrieve the keys of all invoices from May 30, 2010
    $result = $client->add($bucket->name)
        ->key_filter(array('tokenize', '.', 1), array('eq', 'invoice'))
        ->key_filter_and(array('tokenize', '.', 2), array('ends_with', '20100530'))
        ->map("function (v) { return [v.key]; }")
        ->reduce("Riak.reduceSort")
        ->run();

This method returns an array of data representing the result of the Map/Reduce functions.

## Using Search ##
Searches can be executed using the Riak::search() method

    # Create some test data
    $bucket = $client->bucket("searchbucket");
    $bucket->newObject("one", array("foo"=>"one", "bar"=>"red"))->store();
    $bucket->newObject("two", array("foo"=>"two", "bar"=>"green"))->store();

    # Execute a search for all objects with matching properties
    $results = $client->search("searchbucket", "foo:one OR foo:two")->run();

This method will return null unless executed against a Riak Search cluster.

## Meta Data ##
You can provide meta data on objects using Object::getMeta() and Object::setMeta()

    # Set some new meta data
    $object->setMeta("some-meta", "some-value");
    
    # Get some meta data (returns null if not found)
    $object->getMeta("some-meta");
    
    # Get all meta data (an array keyed by meta name)
    $object->getAllMeta()

Remove existing metadata

    # Remove a single value
    $object->removeMeta("some-meta");
    
    # Remove all meta data
    $object->removeAllMeta();

## Secondary Indexes ##

### Adding Secondary Indexes ###
Secondary indexes can be added using the Object::addIndex() and Object::addAutoIndex() methods.  

Auto indexes are kept fresh with the associated field automatically, so if you read an object, modify its data, and write it back, the auto index will reflect the new value from the object.  Traditional indexes are fixed and must be manually managed.  *NOTE* that auto indexes are a function of the Riak PHP client, and are not part of native Riak functionality.  Other clients writing the same object must manage the index manually.

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

You can remove a specific value from an index, all values from an index, or all indexes:

    # Remove just a single value
    $object->removeIndex("index_name", "int", 2);
    
    # Remove all values from an index
    $object->removeAllIndexes("index_name", "int");
    
    # Remove all index types for a given index name
    $object->removeAllIndexes("index_name");
    
    # Remove all indexes
    $object->removeAllIndexes();

Likewise you can remove auto indexes:

    # Just the "foo" index
    $object->removeAutoIndex("foo", "int");
    
    # All auto indexes
    $object->removeAllAutoIndexes("foo", "int");
    
    # All auto indexes
    $object->removeAllAutoIndexes();

Mass load indexes, or just replace an existing index:

    $object->setIndex("index_name", "int", array(1, 2, 3));
    $object->setIndex("text_index", "bin", "foo");

### Querying a Bucket's secondary index ###
Secondary indexes can be queried using the Bucket::indexSearch() method.  This returns an array of Link objects.

    # Exact Match
    $results = $bucket->indexSearch("index_name", "int", 1);
    foreach ($results as $link) {
        echo "Key: {$link->getKey()}<br/>";
        $object = $link->get();
    }
    
    # Range Search
    $results = $bucket->indexSearch("index_name", "int", 1, 10);

Duplicate entries may be found in a ranged index search if a given index has multiple values that fall within the range.  You can request that these duplicates be eliminated in the result.

    $results = $bucket->indexSearch("index_name", "int", 1, 10, true);

### Secondary Indexes in Map/Reduce ###
The same search format used for Bucket::indexSearch() may be used during Map/Reduce operations during the input phase.  This is only valid for bucket-level operations, and cannot be combined with other filtration methods such as key filters.

    # Use secondary indexes to speed up our Map/Reduce operation
    $result = $client
        ->add("bucket_name") // Begin Map/Reduce
        ->indexSearch("index_name", "int", 1)
        ->map("function (v) { return [v.key]; }")
        ->reduce("Riak.reduceSort")
        ->run();

## Additional Resources ##

See TestSuite.php for more examples.<br/>
<https://github.com/basho/riak-php-client/blob/master/tests/integration/TestSuite.php>

## Documentation Maintenance

The PHP API documentation should be regenerated upon each new client release or each new non-trivial API change.

Currently the docs are generated using a tool called [phpDocumentor2](http://www.phpdoc.org), stored in the gh-pages branch of this repo, and are hosted at [http://basho.github.com/riak-php-client/](basho.github.io/riak-php-client/).

### Generating the PHP Documentation

1. Make sure your local copy of this repository is up to date with the latest release/changes.

2. Install phpDocumentor2 (at least 2.0.0b7). 

		$ pear channel-discover pear.phpdoc.org
		$ pear install phpdoc/phpDocumentor-beta
		
3. Install [GraphViz](http://www.graphviz.org/Download..php) (see link for documentation/downloads)

4. Now that you've got phpDocumentor2 (and GraphViz) installed, generating the new documentation is easy. The configuration is specified in the file "phpdoc.xml", and will be used automatically.

		$ php generate-docs.php

5. This should produce a new "docs" directory packed with all sorts of goodness. The next step is to update the "gh-pages" branch:

		$ mv docs /tmp/riak-php-docs
		$ git checkout gh-pages
		$ git rm -rf *
		$ mv /tmp/riak-php-docs/* . && rm -rf /tmp/riak-php-docs
		$ rm -Rf docs/phpdoc-cache-*

6. Add, commit and push everything:

		$ git add .
		$ git commit -m "updated docs"
		$ git push origin gh-pages

Once you push your changes to the gh-pages branch they will be synced to [http://basho.github.com/riak-php-client/](http://basho.github.com/riak-php-client/)

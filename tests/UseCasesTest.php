<?php
use Basho\Riak\Properties;
/**
 * Uses cases for entire framework
 */
class UseCasesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = new \Basho\Riak\Client(HOST, PORT);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->client = null;
    }
    /**
     * @test
     */
    public function isAlive()
    {
        $this->assertTrue($this->client->isAlive(), 'check server live status');
    }
    /**
     * @test
     */
    public function storeAndGet()
    {
        $bucket = $this->client->bucket('bucket');

        $rand = rand();
        $obj = $bucket->newObject('foo', $rand);
        $obj->store();

        $obj = $bucket->get('foo');

        $this->assertTrue($obj->exists(), 'object must be exists');
        $this->assertEquals('bucket', $obj->getBucket()->getName(), 'check bucket name');
        $this->assertEquals('foo', $obj->getKey(), 'check key name');
        $this->assertEquals($rand, $obj->getData(), 'check cell data');
    }
    /**
     * @test
     */
    public function storeAndGetWithoutKey()
    {
        $bucket = $this->client->bucket('bucket');

        $rand = rand();
        $obj = $bucket->newObject(null, $rand);
        $obj->store();

        $key = $obj->getKey();

        $obj = $bucket->get($key);

        $this->assertTrue($obj->exists(), 'object must be exists');
        $this->assertEquals('bucket', $obj->getBucket()->getName(), 'check bucket name');
        $this->assertEquals($key, $obj->getKey(), 'check key name');
        $this->assertEquals($rand, $obj->getData(), 'check cell data');
    }
    /**
     * @test
     */
    public function binaryStoreAndGet()
    {
        $bucket = $this->client->bucket('bucket');

        // Store as binary, retrieve as binary, then compare...
        $rand = rand();
        $obj = $bucket->newBinary('foo1', $rand);
        $obj->store();
        $obj = $bucket->getBinary('foo1');
        $this->assertTrue($obj->exists(), 'object must be exists');
        $this->assertEquals($rand, $obj->getData(), 'check cell data');

        //Store as JSON, retrieve as binary, JSON-decode, then compare...
        $data = array(rand(), rand(), rand());
        $obj = $bucket->newObject('foo2', $data);
        $obj->store();
        $obj = $bucket->getBinary('foo2');
        $this->assertEquals($data, json_decode($obj->getData()), 'check cell JSON data');
    }
    /**
     * @test
     */
    public function missingObject()
    {
        $bucket = $this->client->bucket('bucket');
        $obj = $bucket->get('missing');
        $this->assertFalse($obj->exists(), 'object must be NOT exists');
        $this->assertNull($obj->getData(), 'check cell data - must be null');
    }
    /**
     * @test
     */
    public function delete()
    {
        $bucket = $this->client->bucket('bucket');

        $rand = rand();
        $obj = $bucket->newObject('foo', $rand);
        $obj->store();

        $obj = $bucket->get('foo');
        $this->assertTrue($obj->exists(), 'object must be exists');

        $obj->delete();
        $obj->reload();
        $this->assertFalse($obj->exists(), 'object must be NOT exists - cell is removed');
    }
    /**
     * @test
     */
    public function setBucketProperties()
    {
        $bucket = $this->client->bucket('bucket');

        // Test setting allow mult...
        $properties = new Properties($this->client, $bucket);
        $properties->setAllowMultiples(true);

        $this->assertTrue($properties->getAllowMultiples(), 'check allowMultiples property - checked');

        // Test setting nval...
        $properties->setNVal(3);
        $this->assertEquals(3, $properties->getNVal(), 'check NVal');

        // Test setting multiple properties...
        $properties->setProperties(array('allow_mult' => false, 'n_val' => 2));
        $this->assertFalse($properties->getAllowMultiples(), 'check allowMultiples property - unchecked');
        $this->assertEquals(2, $properties->getNVal(), 'check new NVal');
    }
    /**
     * @test
     */
    public function siblings()
    {
        // Set up the bucket, clear any existing object...
        $bucket = $this->client->bucket('multiBucket');
        $properties = new Properties($this->client, $bucket);
        $properties->setAllowMultiples('true');
        $obj = $bucket->get('foo');
        $obj->delete();

        // Store the same object multiple times...
        for ($i = 0; $i < 5; $i++) {
            $client = new \Basho\Riak\Client(HOST, PORT);
            $bucket = $client->bucket('multiBucket');
            $obj = $bucket->newObject('foo', rand());
            $obj->store();
        }

        // Make sure the object has 5 siblings...
        $this->assertTrue($obj->hasSiblings(), 'check hasSiblings flag');
        $this->assertEquals(5, $obj->getSiblingCount(), 'check siblings count');

        // Test getSibling()/getSiblings()...
        $siblings = $obj->getSiblings();
        $obj3 = $obj->getSibling(3);
        $this->assertEquals($siblings[3]->getData(),
                $obj3->getData(), 'check equal data');

        // Resolve the conflict, and then do a get...
        $obj3 = $obj->getSibling(3);
        $obj3->store();
        $obj->reload();
        $this->assertEquals($obj->getData(),
                $obj3->getData(), 'check equal data after update');

        // Clean up for next test...
        $obj->delete();
    }
    /**
     * @test
     */
    public function javascriptSourceMap()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();

        // Run the map...
        $result = $this->client->add('bucket', 'foo')
                ->map('function (v) { return [JSON.parse(v.values[0].data)]; }')
                ->run();
        $this->assertEquals(array(2), $result, 'check result');
    }
    /**
     * @test
     */
    public function javascriptNamedMap()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();

        // Run the map...
        $result = $this->client->add('bucket', 'foo')->map('Riak.mapValuesJson')
                ->run();
        $this->assertEquals(array(2), $result, 'check result');
    }
    /**
     * @test
     */
    public function javascriptSourceMapReduce()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();
        $bucket->newObject('bar', 3)->store();
        $bucket->newObject('baz', 4)->store();

        // Run the map...
        $result = $this->client->add('bucket', 'foo')->add('bucket', 'bar')
                ->add('bucket', 'baz')->map('function (v) { return [1]; }')
                ->reduce('Riak.reduceSum')->run();
        $this->assertEquals(3, $result[0], 'check result');
    }
    /**
     * @test
     */
    public function javascriptNamedMapReduce()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();
        $bucket->newObject('bar', 3)->store();
        $bucket->newObject('baz', 4)->store();

        // Run the map...
        $result = $this->client->add('bucket', 'foo')->add('bucket', 'bar')
                ->add('bucket', 'baz')->map('Riak.mapValuesJson')
                ->reduce('Riak.reduceSum')->run();
        $this->assertEquals(array(9), $result, 'check result');
    }
    /**
     * @test
     */
    public function javascriptBucketMapReduce()
    {
        $bucket = $this->client->bucket('bucket_' . rand());
        $bucket->newObject('foo', 2)->store();
        $bucket->newObject('bar', 3)->store();
        $bucket->newObject('baz', 4)->store();

        // Run the map...
        $result = $this->client
            ->add($bucket->getName())
            ->map('Riak.mapValuesJson')
            ->reduce('Riak.reduceSum')
            ->run();

        $this->assertEquals(array(9), $result, 'check result');
    }
    /**
     * @test
     */
    public function javascriptArgMapReduce()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();

        // Run the map...
        $result = $this->client->add('bucket', 'foo', 5)
                ->add('bucket', 'foo', 10)
                ->add('bucket', 'foo', 15)->add('bucket', 'foo', -15)
                ->add('bucket', 'foo', -5)
                ->map('function(v, arg) { return [arg]; }')
                ->reduce('Riak.reduceSum')->run();
        $this->assertEquals(array(10), $result, 'check result');
    }
    /**
     * @test
     */
    public function erlangMapReduce()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();
        $bucket->newObject('bar', 2)->store();
        $bucket->newObject('baz', 4)->store();

        // Run the map...
        $result = $this->client->add('bucket', 'foo')->add('bucket', 'bar')
                ->add('bucket', 'baz')
                ->map(array('riak_kv_mapreduce', 'map_object_value'))
                ->reduce(array('riak_kv_mapreduce', 'reduce_set_union'))->run();
        $this->assertEquals(2, count($result), 'check result');
    }
    /**
     * @test
     */
    public function mapReduceFromObject()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->store();

        $obj = $bucket->get('foo');
        $result = $obj->map('Riak.mapValuesJson')->run();
        $this->assertEquals(array(2), $result, 'check result');
    }
    /**
     * @test
     */
    public function keyFilter()
    {
        // Create the object...
        $client = new \Basho\Riak\Client(HOST, PORT);
        $bucket = $client->bucket('filter_bucket');
        $bucket->newObject('foo_one', array('foo' => 'one'))->store();
        $bucket->newObject('foo_two', array('foo' => 'two'))->store();
        $bucket->newObject('foo_three', array('foo' => 'three'))->store();
        $bucket->newObject('foo_four', array('foo' => 'four'))->store();
        $bucket->newObject('moo_five', array('foo' => 'five'))->store();

        $mapred = $client->add($bucket->getName())
                ->keyFilter(array('tokenize', '_', 1), array('eq', 'foo'));
        if ($mapred instanceof MapReduce) {
            $results = $mapred->run();
        } else {
            $results = 0;
        }
        $this->assertEquals(4, count($results), 'check result');
    }
    /**
     * @notest
     */
    public function keyFilterOperator()
    {
        // Create the object...
        $client = new \Basho\Riak\Client(HOST, PORT);
        $bucket = $client->bucket('filter_bucket');
        $bucket->newObject('foo_one', array('foo' => 'one'))->store();
        $bucket->newObject('foo_two', array('foo' => 'two'))->store();
        $bucket->newObject('foo_three', array('foo' => 'three'))->store();
        $bucket->newObject('foo_four', array('foo' => 'four'))->store();
        $bucket->newObject('moo_five', array('foo' => 'five'))->store();

        $mapred = $client->add($bucket->getName())
                ->keyFilter(array('starts_with', 'foo'))
                ->keyFilterOr(array('ends_with', 'five'));
        $results = $mapred->run();
        $this->assertEquals(5, count($results), 'check result');
    }
    /**
     * @test
     */
    public function storeAndGetLinks()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)->addLink($bucket->newObject('foo1'))
                ->addLink($bucket->newObject('foo2'), 'tag')
                ->addLink($bucket->newObject('foo3'), 'tag2!@//$%^&*')->store();

        $obj = $bucket->get('foo');
        $links = $obj->getLinks();
        $this->assertEquals(3, count($links), 'check links number');
    }
    /**
     * @test
     */
    public function linkWalking()
    {
        $bucket = $this->client->bucket('bucket');
        $bucket->newObject('foo', 2)
                ->addLink($bucket->newObject('foo1', 'test1')->store())
                ->addLink($bucket->newObject('foo2', 'test2')->store(), 'tag')
                ->addLink($bucket->newObject('foo3', 'test3')->store(),
                        'tag2!@//$%^&*')->store();

        $obj = $bucket->get('foo');
        $results = $obj->link('bucket')->run();
        $this->assertEquals(3, count($results), 'check results length');

        $results = $obj->link('bucket', 'tag')->run();
        $this->assertEquals(1, count($results), 'check new results length');
    }
    /**
     * @test
     */
    public function searchIntegration()
    {
        $bucket = $this->client->bucket('searchbucket');
        $bucket->newObject('one', array('foo' => 'one', 'bar' => 'red'))
                ->store();
        $bucket->newObject('two', array('foo' => 'two', 'bar' => 'green'))
                ->store();
        $bucket->newObject('three', array('foo' => 'three', 'bar' => 'blue'))
                ->store();
        $bucket->newObject('four', array('foo' => 'four', 'bar' => 'orange'))
                ->store();
        $bucket->newObject('five', array('foo' => 'five', 'bar' => 'yellow'))
                ->store();

        // Run some operations...
        $results = $this->client
                ->search('searchbucket', 'foo:one OR foo:two')->run();
        if (count($results) == 0) {
            $this->markTestSkipped(
            'Not running test "testSearchIntegration()"'
            . PHP_EOL
            . 'Please ensure that you have installed the Riak Search hook on
               bucket "searchbucket" by running "bin/search-cmd install
               searchbucket"');
        }

        $this->assertEquals(2, count($results), 'check results length');

        $results = $this->client
        ->search('searchbucket', '(foo:one OR foo:two OR foo:three OR foo:four) AND (NOT bar:green)')
        ->run();
        $this->assertEquals(3, count($results), 'check new results length');
    }
    /**
     * @test
     */
    public function secondaryIndexes()
    {
        $bucket = $this->client->bucket("indextest");

        # Immediate test to see if 2i is even supported w/ the backend
        try {
            $bucket->indexSearch("foo", "bar_bin", "baz");
        } catch (Exception $e) {
            if (strpos($e->__toString(), "indexes_not_supported") !== false) {
                return true;
            } else {
                throw $e;
            }
        }

        # Okay, continue with the rest of the test
        $bucket->newObject("one", array("foo" => 1, "bar" => "red"))
        ->addIndex("number", "int", 1)->addIndex("text", "bin", "apple")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("two", array("foo" => 2, "bar" => "green"))
        ->addIndex("number", "int", 2)->addIndex("text", "bin", "avocado")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("three", array("foo" => 3, "bar" => "blue"))
        ->addIndex("number", "int", 3)
        ->addIndex("text", "bin", "blueberry")->addAutoIndex("foo", "int")
        ->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("four", array("foo" => 4, "bar" => "orange"))
        ->addIndex("number", "int", 4)->addIndex("text", "bin", "citrus")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("five", array("foo" => 5, "bar" => "yellow"))
        ->addIndex("number", "int", 5)->addIndex("text", "bin", "banana")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("six", array("foo" => 6, "bar" => "purple"))
        ->addIndex("number", "int", 6)->addIndex("number", "int", 7)
        ->addIndex("number", "int", 8)
        ->setIndex("text", "bin", array("x", "y", "z"))->store();

        # Exact matches
        $results = $bucket->indexSearch("number", "int", 5);
        $this->assertEquals(1, count($results));

        $results = $bucket->indexSearch("text", "bin", "apple");
        $this->assertEquals(1, count($results));

        # Range searches
        $results = $bucket->indexSearch("foo", "int", 1, 3);
        $this->assertEquals(3, count($results));

        $results = $bucket->indexSearch("bar", "bin", "blue", "orange");
        $this->assertEquals(3, count($results));

        # Test duplicate key de-duping
        $results = $bucket->indexSearch("number", "int", 6, 8, true);
        $this->assertEquals(1, count($results));

        $results = $bucket->indexSearch("text", "bin", "x", "z", true);
        $this->assertEquals(1, count($results));

        # Test auto indexes don't leave cruft indexes behind, and regular
        # indexes are preserved
        $object = $bucket->get("one");
        $object->setData(array("foo" => 9, "bar" => "plaid"));
        $object->store();

        # Auto index updates
        $results = $bucket->indexSearch("foo", "int", 9);
        $this->assertEquals(1, count($results));

        # Auto index leaves no cruft
        $results = $bucket->indexSearch("foo", "int", 1);
        $this->assertEquals(0, count($results));

        # Normal index is preserved
        $results = $bucket->indexSearch("number", "int", 1);
        $this->assertEquals(1, count($results));

        # Test proper collision handling on autoIndex
        # and regular index on same field
        $bucket->newObject("seven", array("foo" => 7))
            ->addAutoIndex("foo", "int")->addIndex("foo", "int", 7)->store();

        $results = $bucket->indexSearch("foo", "int", 7);
        $this->assertEquals(1, count($results));

        $object = $bucket->get("seven");
        $object->setData(array("foo" => 8));
        $object->store();

        $results = $bucket->indexSearch("foo", "int", 8);
        $this->assertEquals(1, count($results));

        $results = $bucket->indexSearch("foo", "int", 7);
        $this->assertEquals(1, count($results));
    }
    /**
     * @test
     */
    public function secondaryIndexMapReduce()
    {
        $bucket = $this->client->bucket("index-mapred-test");
        # Setup
        $bucket->newObject("one", array("foo" => 1, "bar" => "red"))
        ->addIndex("number", "int", 1)->addIndex("text", "bin", "apple")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("two", array("foo" => 2, "bar" => "green"))
        ->addIndex("number", "int", 2)->addIndex("text", "bin", "avocado")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("three", array("foo" => 3, "bar" => "blue"))
        ->addIndex("number", "int", 3)
        ->addIndex("text", "bin", "blueberry")->addAutoIndex("foo", "int")
        ->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("four", array("foo" => 4, "bar" => "orange"))
        ->addIndex("number", "int", 4)->addIndex("text", "bin", "citrus")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("five", array("foo" => 5, "bar" => "yellow"))
        ->addIndex("number", "int", 5)->addIndex("text", "bin", "banana")
        ->addAutoIndex("foo", "int")->addAutoIndex("bar", "bin")->store();

        $bucket->newObject("six", array("foo" => 6, "bar" => "purple"))
        ->addIndex("number", "int", 6)->addIndex("number", "int", 7)
        ->addIndex("number", "int", 8)
        ->setIndex("text", "bin", array("x", "y", "z"))->store();

        # Test
        $result = $this->client->add($bucket->getName())
            ->indexSearch('number', 'int', 6)->run();
        $this->assertEquals(1, count($result));

        $result = $this->client->add($bucket->getName())
            ->indexSearch('number', 'int', 9)->run();
        $this->assertEquals(0, count($result));

        $result = $this->client->add($bucket->getName())
        ->indexSearch('text', 'bin', 'banana')->run();
        $this->assertEquals(1, count($result));

        # Test ranges
        $result = $this->client->add($bucket->getName())
        ->indexSearch('number', 'int', 1, 4)->run();
        $this->assertEquals(4, count($result));

        $result = $this->client->add($bucket->getName())
        ->indexSearch('number', 'int', 6, 8)->run();
        # I'm not convinced this result is correct, a range
        # search matching multiple values of the same index
        # on the same document should return that document
        # multiple times?  Nonetheless it's consistent with
        # direct index search against the bucket, and the
        # problem would come out of the way Riak does index
        # searches on the server.
        $this->assertEquals(3, count($result));
    }
    /**
     * @test
     */
    public function metaData()
    {
        $client = new \Basho\Riak\Client(HOST, PORT);
        $bucket = $client->bucket("metatest");

        # Set some meta
        $bucket->newObject("metatest", array("foo" => 'bar'))
        ->setMeta("foo", "bar")->store();

        # Test that we load the meta back
        $object = $bucket->get("metatest");
        $this->assertEquals($object->getMeta("foo"), "bar");

        # Test that the meta is preserved when we rewrite the object
        $bucket->get("metatest")->store();
        $object = $bucket->get("metatest");
        $this->assertEquals($object->getMeta("foo"), "bar");

        # Test that we remove meta
        $object->removeMeta("foo")->store();
        $anotherObject = $bucket->get("metatest");
        $this->assertEquals($anotherObject->getMeta("foo"), null);
    }
}

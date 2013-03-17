<?php
use Basho\Riak\Bucket, Basho\Riak\Client;
/**
 * Test class for Bucket.
 */
class BucketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bucket
     */
    protected $bucket;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->bucket = new Bucket(new Client(), "test");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->bucket = null;
    }

    /**
     * @todo Implement testGetName().
     */
    public function testGetName()
    {
        $this->assertEquals('test', $this->bucket->getName());
    }

    /**
     * @test
     */
    public function getR()
    {
        $this->assertTrue($this->bucket->getR() == 2);
    }

    /**
     * @test
     */
    public function setR()
    {
        $this->bucket->setR(3);
        $this->assertTrue($this->bucket->getR() == 3);
    }

    /**
     * @test
     */
    public function getW()
    {
        $this->assertTrue($this->bucket->getW() == 2);
    }

    /**
     * @test
     */
    public function setW()
    {
        $this->bucket->setW(3);
        $this->assertTrue($this->bucket->getW() == 3);
    }

    /**
     * @test
     */
    public function getDW()
    {
        $this->assertTrue($this->bucket->getDW() == 2);
    }

    /**
     * @test
     */
    public function setDW()
    {
        $this->bucket->setDW(3);
        $this->assertTrue($this->bucket->getDW() == 3);
    }

    /**
     * @covers Basho\Riak\Bucket::newObject
     * @test
     */
    public function newObject()
    {
        $object = $this->bucket->newObject('test');
        $this->assertInstanceOf('Basho\Riak\Object', $object);
        $this->assertTrue($object->getContentType() == 'application/json');
    }

    /**
     * @covers Basho\Riak\Bucket::newBinary
     * @test
     */
    public function newBinary()
    {
        $object = $this->bucket->newBinary('test', 'Data');
        $this->assertInstanceOf('Basho\Riak\Object', $object);
        $this->assertTrue($object->getData() == 'Data');
    }

    /**
     * @covers Basho\Riak\Bucket::get
     * @test
     */
    public function get()
    {
        $object = $this->bucket->newObject('test');
        $this->assertInstanceOf('Basho\Riak\Object', $this->bucket->get('test'));
    }

    /**
     * @covers Basho\Riak\Bucket::getBinary
     * @test
     */
    public function getBinary()
    {
        $object = $this->bucket->newBinary('test', 'Data');
        $this->assertInstanceOf('Basho\Riak\Object', $this->bucket->getBinary('test'));
    }

    /**
     * @covers Basho\Riak\Bucket::getProperties
     * @test
     */
    public function getProperties()
    {
        $this->assertInstanceOf('Basho\Riak\Properties', $this->bucket->getProperties());
    }

    /**
     * @covers Basho\Riak\Bucket::getKeys
     * @test
     */
    public function getKeys()
    {
        $this->assertTrue(is_array($this->bucket->getKeys()));
    }

    /**
     * @covers Basho\Riak\Bucket::indexSearch
     * @todo Implement testIndexSearch().
     * @test
     */
    public function indexSearch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
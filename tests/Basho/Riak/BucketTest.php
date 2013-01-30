<?php
use Basho\Riak\Bucket, Basho\Riak\Client;
/**
 * Test class for Bucket.
 */
class BucketTest extends PHPUnit_Framework_TestCase
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
     * @covers Bucket::getName
     * @todo Implement testGetName().
     */
    public function testGetName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getR
     * @todo Implement testGetR().
     * @test
     */
    public function getR()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setR
     * @todo Implement testSetR().
     * @test
     */
    public function setR()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getW
     * @todo Implement testGetW().
     * @test
     */
    public function getW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setW
     * @todo Implement testSetW().
     * @test
     */
    public function setW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getDW
     * @todo Implement testGetDW().
     * @test
     */
    public function getDW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setDW
     * @todo Implement testSetDW().
     * @test
     */
    public function setDW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::newObject
     * @todo Implement testNewObject().
     * @test
     */
    public function newObject()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::newBinary
     * @todo Implement testNewBinary().
     * @test
     */
    public function newBinary()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::get
     * @todo Implement testGet().
     * @test
     */
    public function get()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getBinary
     * @todo Implement testGetBinary().
     * @test
     */
    public function getBinary()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setNVal
     * @todo Implement testSetNVal().
     * @test
     */
    public function setNVal()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getNVal
     * @todo Implement testGetNVal().
     * @test
     */
    public function getNVal()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setAllowMultiples
     * @todo Implement testSetAllowMultiples().
     * @test
     */
    public function setAllowMultiples()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getAllowMultiples
     * @todo Implement testGetAllowMultiples().
     * @test
     */
    public function getAllowMultiples()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setProperty
     * @todo Implement testSetProperty().
     * @test
     */
    public function setProperty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getProperty
     * @todo Implement testGetProperty().
     * @test
     */
    public function getProperty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::setProperties
     * @todo Implement testSetProperties().
     * @test
     */
    public function setProperties()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getProperties
     * @todo Implement testGetProperties().
     * @test
     */
    public function getProperties()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::getKeys
     * @todo Implement testGetKeys().
     * @test
     */
    public function getKeys()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Bucket::indexSearch
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
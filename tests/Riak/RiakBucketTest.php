<?php
/**
 * Test class for RiakBucket.
 */
class RiakBucketTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakBucket
     */
    protected $bucket;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->bucket = new RiakBucket(new RiakClient(), "test");
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
     * @covers RiakBucket::getName
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
     * @covers RiakBucket::getR
     * @todo Implement testGetR().
     */
    public function testGetR()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setR
     * @todo Implement testSetR().
     */
    public function testSetR()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getW
     * @todo Implement testGetW().
     */
    public function testGetW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setW
     * @todo Implement testSetW().
     */
    public function testSetW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getDW
     * @todo Implement testGetDW().
     */
    public function testGetDW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setDW
     * @todo Implement testSetDW().
     */
    public function testSetDW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::newObject
     * @todo Implement testNewObject().
     */
    public function testNewObject()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::newBinary
     * @todo Implement testNewBinary().
     */
    public function testNewBinary()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::get
     * @todo Implement testGet().
     */
    public function testGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getBinary
     * @todo Implement testGetBinary().
     */
    public function testGetBinary()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setNVal
     * @todo Implement testSetNVal().
     */
    public function testSetNVal()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getNVal
     * @todo Implement testGetNVal().
     */
    public function testGetNVal()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setAllowMultiples
     * @todo Implement testSetAllowMultiples().
     */
    public function testSetAllowMultiples()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getAllowMultiples
     * @todo Implement testGetAllowMultiples().
     */
    public function testGetAllowMultiples()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setProperty
     * @todo Implement testSetProperty().
     */
    public function testSetProperty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getProperty
     * @todo Implement testGetProperty().
     */
    public function testGetProperty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::setProperties
     * @todo Implement testSetProperties().
     */
    public function testSetProperties()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getProperties
     * @todo Implement testGetProperties().
     */
    public function testGetProperties()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::getKeys
     * @todo Implement testGetKeys().
     */
    public function testGetKeys()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakBucket::indexSearch
     * @todo Implement testIndexSearch().
     */
    public function testIndexSearch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
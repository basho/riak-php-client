<?php
/**
 * Test class for RiakLink.
 */
class RiakLinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakLink
     */
    protected $link;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->link = new RiakLink("test", "test");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    	$this->link = null;
    }

    /**
     * @covers RiakLink::get
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
     * @covers RiakLink::getBinary
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
     * @covers RiakLink::getBucket
     * @todo Implement testGetBucket().
     */
    public function testGetBucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::setBucket
     * @todo Implement testSetBucket().
     */
    public function testSetBucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::getKey
     * @todo Implement testGetKey().
     */
    public function testGetKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::setKey
     * @todo Implement testSetKey().
     */
    public function testSetKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::getTag
     * @todo Implement testGetTag().
     */
    public function testGetTag()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::setTag
     * @todo Implement testSetTag().
     */
    public function testSetTag()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::toLinkHeader
     * @todo Implement testToLinkHeader().
     */
    public function testToLinkHeader()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakLink::isEqual
     * @todo Implement testIsEqual().
     */
    public function testIsEqual()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
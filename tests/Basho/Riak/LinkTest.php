<?php
use Basho\Riak\Link;
/**
 * Test class for Link.
 */
class LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Link
     */
    protected $link;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->link = new Link("test", "test");
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
     * @covers Link::get
     * @todo Implement testGet().
     * @test
     */
    public function get()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::getBinary
     * @todo Implement testGetBinary().
     * @test
     */
    public function getBinary()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::getBucket
     * @todo Implement testGetBucket().
     * @test
     */
    public function getBucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::setBucket
     * @todo Implement testSetBucket().
     * @test
     */
    public function setBucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::getKey
     * @todo Implement testGetKey().
     * @test
     */
    public function getKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::setKey
     * @todo Implement testSetKey().
     * @test
     */
    public function setKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::getTag
     * @todo Implement testGetTag().
     * @test
     */
    public function getTag()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::setTag
     * @todo Implement testSetTag().
     */
    public function testSetTag()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::toLinkHeader
     * @todo Implement testToLinkHeader().
     * @test
     */
    public function toLinkHeader()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Link::isEqual
     * @todo Implement testIsEqual().
     * @test
     */
    public function isEqual()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

<?php
/**
 * Test class for RiakMapReduce.
 */
class RiakMapReduceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakMapReduce
     */
    protected $mapReduce;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->mapReduce = new RiakMapReduce(new RiakClient());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    	$this->mapReduce = null;
    }

    /**
     * @covers RiakMapReduce::add
     * @todo Implement testAdd().
     */
    public function testAdd()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::search
     * @todo Implement testSearch().
     */
    public function testSearch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::link
     * @todo Implement testLink().
     */
    public function testLink()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::map
     * @todo Implement testMap().
     */
    public function testMap()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::reduce
     * @todo Implement testReduce().
     */
    public function testReduce()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::keyFilter
     * @todo Implement testKeyFilter().
     */
    public function testKeyFilter()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::keyFilterAnd
     * @todo Implement testKeyFilterAnd().
     */
    public function testKeyFilterAnd()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::keyFilterOr
     * @todo Implement testKeyFilterOr().
     */
    public function testKeyFilterOr()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::keyFilterOperator
     * @todo Implement testKeyFilterOperator().
     */
    public function testKeyFilterOperator()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::indexSearch
     * @todo Implement testIndexSearch().
     */
    public function testIndexSearch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakMapReduce::run
     * @todo Implement testRun().
     */
    public function testRun()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
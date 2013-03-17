<?php
use Basho\Riak\MapReduce, Basho\Riak\Client;
/**
 * Test class for MapReduce.
 */
class MapReduceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MapReduce
     */
    protected $mapReduce;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->mapReduce = new MapReduce(new Client());
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
     * @covers MapReduce::add
     * @todo Implement testAdd().
     * @test
     */
    public function add()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::search
     * @todo Implement testSearch().
     * @test
     */
    public function search()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::link
     * @todo Implement testLink().
     * @test
     */
    public function link()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::map
     * @todo Implement testMap().
     * @test
     */
    public function map()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::reduce
     * @todo Implement testReduce().
     * @test
     */
    public function reduce()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::keyFilter
     * @todo Implement testKeyFilter().
     * @test
     */
    public function keyFilter()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::keyFilterAnd
     * @todo Implement testKeyFilterAnd().
     * @test
     */
    public function keyFilterAnd()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::keyFilterOr
     * @todo Implement testKeyFilterOr().
     * @test
     */
    public function keyFilterOr()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::keyFilterOperator
     * @todo Implement testKeyFilterOperator().
     * @test
     */
    public function keyFilterOperator()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::indexSearch
     * @todo Implement testIndexSearch().
     * @test
     */
    public function indexSearch()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers MapReduce::run
     * @todo Implement testRun().
     */
    public function testRun()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }
}

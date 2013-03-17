<?php
use Basho\Riak\MapReduce\Phase;
/**
 * Test class for MapReducePhase.
 */
class MapReducePhaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phase
     */
    protected $phase;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->phase = new Phase('map', 'function',
                'javascript', true, 'args');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->phase = null;
    }

    /**
     * @covers Basho\Riak\MapReduce\Phase::toArray
     * @todo Implement testToArray().
     * @test
     */
    public function toArray()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

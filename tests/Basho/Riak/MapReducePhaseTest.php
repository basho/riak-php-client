<?php
use Basho\Riak\MapReducePhase;
/**
 * Test class for MapReducePhase.
 */
class MapReducePhaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MapReducePhase
     */
    protected $mapReducePhase;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->mapReducePhase = new MapReducePhase('map', 'function',
                'javascript', true, 'args');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->mapReducePhase = null;
    }

    /**
     * @covers MapReducePhase::toArray
     * @todo Implement testToArray().
     * @test
     */
    public function toArray()
    {
        // Remove the following lines when you implement this test.
        $this
                ->markTestIncomplete('This test has not been implemented yet.');
    }
}

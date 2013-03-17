<?php
use Basho\Riak\Link\Phase;
/**
 * Test class for LinkPhase.
 */
class LinkPhaseTest extends PHPUnit_Framework_TestCase
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
        $this->phase = new Phase("test", "test", true);
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
     * @covers Basho\Riak\Link\Phase::toArray
     * @test
     */
    public function toArray()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

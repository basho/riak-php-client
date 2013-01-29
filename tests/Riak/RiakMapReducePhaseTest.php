<?php
/**
 * Test class for RiakMapReducePhase.
 */
class RiakMapReducePhaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakMapReducePhase
     */
    protected $mapReducePhase;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->mapReducePhase = new RiakMapReducePhase('map', 'function', 'javascript', true, 'args');
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
     * @covers RiakMapReducePhase::toArray
     * @todo Implement testToArray().
     */
    public function testToArray()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
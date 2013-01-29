<?php
/**
 * Test class for RiakLinkPhase.
 */
class RiakLinkPhaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakLinkPhase
     */
    protected $linkPhase;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->linkPhase = new RiakLinkPhase("test", "test", true);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    	$this->linkPhase = null;
    }

    /**
     * @covers RiakLinkPhase::toArray
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
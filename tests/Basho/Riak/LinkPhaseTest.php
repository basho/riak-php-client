<?php
use Basho\Riak\LinkPhase;
/**
 * Test class for LinkPhase.
 */
class LinkPhaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LinkPhase
     */
    protected $linkPhase;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->linkPhase = new LinkPhase("test", "test", true);
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
     * @covers LinkPhase::toArray
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

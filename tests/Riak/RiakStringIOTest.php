<?php
/**
 * Test class for RiakStringIO.
 */
class RiakStringIOTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakStringIO
     */
    protected $stringIO;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->stringIO = new RiakStringIO;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    	$this->stringIO = null;
    }

    /**
     * @covers RiakStringIO::write
     * @todo Implement testWrite().
     */
    public function testWrite()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakStringIO::contents
     * @todo Implement testContents().
     */
    public function testContents()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
<?php
use Basho\Riak\StringIO;
/**
 * Test class for StringIO.
 */
class StringIOTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var StringIO
     */
    protected $stringIO;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->stringIO = new StringIO;
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
     * @covers StringIO::write
     * @todo Implement testWrite().
     * @test
     */
    public function write()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers StringIO::contents
     * @todo Implement testContents().
     * @test
     */
    public function contents()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
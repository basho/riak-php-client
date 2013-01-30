<?php
/**
 * Test class for Client.
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = new Basho\Riak\Client();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->client = null;
    }

    /**
     * @covers Client::getR
     * @todo Implement testGetR().
     * @test
     */
    public function getR()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::setR
     * @todo Implement testSetR().
     * @test
     */
    public function setR()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::getW
     * @todo Implement testGetW().
     * @test
     */
    public function getW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::setW
     * @todo Implement testSetW().
     * @test
     */
    public function setW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::getDW
     * @todo Implement testGetDW().
     * @test
     */
    public function getDW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::setDW
     * @todo Implement testSetDW().
     * @test
     */
    public function setDW()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::getClientID
     * @todo Implement testGetClientID().
     * @test
     */
    public function getClientID()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::setClientID
     * @todo Implement testSetClientID().
     * @test
     */
    public function setClientID()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::bucket
     * @todo Implement testBucket().
     * @test
     */
    public function bucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::buckets
     * @todo Implement testBuckets().
     * @test
     */
    public function buckets()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * Client::isAlive
     * @todo Implement testAdd().
     * @test
     */
    public function isAlive()
    {
        //Remove the following lines when you implement this test.
        //$this->markTestIncomplete('This test has not been implemented yet.');
        
        $this->assertTrue($this->client->isAlive(), 'check server live status');
    }

    /**
     * @covers Client::add
     * @todo Implement testAdd().
     * @test
     */
    public function add()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::search
     * @todo Implement testSearch().
     * @test
     */
    public function search()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::link
     * @todo Implement testLink().
     * @test
     */
    public function link()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::map
     * @todo Implement testMap().
     * @test
     */
    public function map()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Client::reduce
     * @todo Implement testReduce().
     * @test
     */
    public function reduce()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

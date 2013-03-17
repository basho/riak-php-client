<?php
use Basho\Riak\Client;
/**
 * Test class for Client.
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Basho\Riak\Client
     */
    protected $client;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = new Client();
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
     * @covers Basho\Riak\Client::getR
     * @test
     */
    public function getR()
    {
        $this->assertTrue($this->client->getR() == 2);
    }

    /**
     * @covers Basho\Riak\Client::SetR
     * @test
     */
    public function setR()
    {
        $this->client->setR(3);
        $this->assertTrue($this->client->getR() == 3);
    }

    /**
     * @covers Basho\Riak\Client::getW
     * @test
     */
    public function getW()
    {
        $this->assertTrue($this->client->getW() == 2);
    }

    /**
     * @covers Basho\Riak\Client::setW
     * @test
     */
    public function setW()
    {
        $this->client->setW(3);
        $this->assertTrue($this->client->getW() == 3);
    }

    /**
     * @covers Basho\Riak\Client::getDW
     * @test
     */
    public function getDW()
    {
        $this->assertTrue($this->client->getDW() == 2);
    }

    /**
     * @covers Basho\Riak\Client::setDW
     * @test
     */
    public function setDW()
    {
        $this->client->setDW(3);
        $this->assertTrue($this->client->getDW() == 3);
    }

    /**
     * @covers Basho\Riak\Client::getClientID
     * @test
     */
    public function getClientID()
    {
        $this->assertEquals('php_', substr($this->client->getClientID(), 0, 4));
    }

    /**
     * @covers Basho\Riak\Client::setClientID
     * @test
     */
    public function setClientID()
    {
        $this->client->setClientID('php5_');
        $this->assertEquals('php5', substr($this->client->getClientID(), 0, 4));
    }

    /**
     * @covers Basho\Riak\Client::bucket
     * @test
     */
    public function bucket()
    {
        $this->assertInstanceOf('Basho\Riak\Bucket', $this->client->bucket('test'));
    }

    /**
     * @covers Basho\Riak\Client::buckets
     * @test
     */
    public function buckets()
    {
        $this->assertTrue(is_array($this->client->buckets()));
    }

    /**
     * @covers Basho\Riak\Client::isAlive
     * @test
     */
    public function isAlive()
    {
        $this->assertTrue($this->client->isAlive(), 'check server live status');
    }

    /**
     * @covers Basho\Riak\Client::add
     * @todo Implement testAdd().
     * @test
     */
    public function add()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Basho\Riak\Client::search
     * @todo Implement testSearch().
     * @test
     */
    public function search()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Basho\Riak\Client::link
     * @todo Implement testLink().
     * @test
     */
    public function link()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Basho\Riak\Client::map
     * @todo Implement testMap().
     * @test
     */
    public function map()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers Basho\Riak\Client::reduce
     * @todo Implement testReduce().
     * @test
     */
    public function reduce()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

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
     * @test
     */
    public function getR()
    {
        $this->assertTrue($this->client->getR() == 2);
    }

    /**
     * @test
     */
    public function setR()
    {
        $this->client->setR(3);
        $this->assertTrue($this->client->getR() == 3);
    }

    /**
     * @test
     */
    public function getW()
    {
        $this->assertTrue($this->client->getW() == 2);
    }

    /**
     * @test
     */
    public function setW()
    {
        $this->client->setW(3);
        $this->assertTrue($this->client->getW() == 3);
    }

    /**
     * @test
     */
    public function getDW()
    {
        $this->assertTrue($this->client->getDW() == 2);
    }

    /**
     * @test
     */
    public function setDW()
    {
        $this->client->setDW(3);
        $this->assertTrue($this->client->getDW() == 3);
    }

    /**
     * @test
     */
    public function getId()
    {
        $this->assertEquals('php_', substr($this->client->getId(), 0, 4));
    }

    /**
     * @test
     */
    public function setId()
    {
        $this->client->setId('php5_');
        $this->assertEquals('php5', substr($this->client->getId(), 0, 4));
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

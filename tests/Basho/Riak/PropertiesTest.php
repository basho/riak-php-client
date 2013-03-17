<?php
use Basho\Riak\Properties, Basho\Riak\Client, Basho\Riak\Bucket;
/**
 * Test class for properties.
 */
class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Properties
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $client = new Client();
        $this->object = new Properties($client, new Bucket($client, "test"));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Basho\Riak\Properties::setNVal
     * @test
     */
    public function setNVal()
    {
        $this->object->setNVal(2);
        $this->assertEquals(2, $this->object->getNVal());
    }

    /**
     * @covers Basho\Riak\Properties::getNVal
     * @test
     */
    public function getNVal()
    {
        $this->assertEquals(2, $this->object->getNVal());
    }

    /**
     * @covers Basho\Riak\Properties::setAllowMultiples
     * @test
     */
    public function setAllowMultiples()
    {
        $this->object->setAllowMultiples(true);
        $this->assertEquals(true, $this->object->getAllowMultiples());
    }

    /**
     * @covers Basho\Riak\Properties::getAllowMultiples
     * @test
     */
    public function getAllowMultiples()
    {
        $this->object->setAllowMultiples(false);
        $this->assertEquals(false, $this->object->getAllowMultiples());
    }

    /**
     * @covers Basho\Riak\Properties::setProperty
     * @test
     */
    public function setProperty()
    {
        $this->object->setProperty("allow_mult", true);
        $this->assertEquals(true, $this->object->getAllowMultiples());
    }

    /**
     * @covers Basho\Riak\Properties::getProperty
     * @test
     */
    public function getProperty()
    {
        $this->object->setProperty("n_val", 3);
        $this->assertEquals(3, $this->object->getProperty("n_val"));
    }

    /**
     * @covers Basho\Riak\Properties::setProperties
     * @test
     */
    public function setProperties()
    {
        $this->object->setProperties(array("n_val"=> 2, "allow_mult" => false));
        $this->assertTrue(is_array($this->object->getProperties()));
        $this->assertEquals(false, $this->object->getAllowMultiples());
        $this->assertEquals(2, $this->object->getNVal());
    }

    /**
     * @covers Basho\Riak\Properties::getProperties
     * @test
     */
    public function getProperties()
    {
        $this->assertTrue(is_array($this->object->getProperties()));
    }
}

<?php
use Basho\Riak\Object, Basho\Riak\Client, Basho\Riak\Bucket;
/**
 * Test class for Object.
 */
class ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Object(new Client(), new Bucket(new Client(), 'test'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object = null;
    }

    /**
     * @covers Object::getBucket
     * @todo Implement testGetBucket().
     * @test
     */
    public function getBucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getKey
     * @todo Implement testGetKey().
     * @test
     */
    public function getKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getData
     * @todo Implement testGetData().
     * @test
     */
    public function getData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::setData
     * @todo Implement testSetData().
     * @test
     */
    public function setData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::status
     * @todo Implement testStatus().
     * @test
     */
    public function status()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::exists
     * @todo Implement testExists().
     * @test
     */
    public function exists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getContentType
     * @todo Implement testGetContentType().
     * @test
     */
    public function getContentType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::setContentType
     * @todo Implement testSetContentType().
     * @test
     */
    public function setContentType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getLastModified
     * @todo Implement testGetLastModified().
     * @test
     */
    public function getLastModified()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::addLink
     * @todo Implement testAddLink().
     * @test
     */
    public function addLink()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeLink
     * @todo Implement testRemoveLink().
     * @test
     */
    public function removeLink()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getLinks
     * @todo Implement testGetLinks().
     * @test
     */
    public function getLinks()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::addIndex
     * @todo Implement testAddIndex().
     * @test
     */
    public function addIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::setIndex
     * @todo Implement testSetIndex().
     * @test
     */
    public function setIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getIndex
     * @todo Implement testGetIndex().
     * @test
     */
    public function getIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeIndex
     * @todo Implement testRemoveIndex().
     * @test
     */
    public function removeIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeAllIndexes
     * @todo Implement testRemoveAllIndexes().
     * @test
     */
    public function removeAllIndexes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::addAutoIndex
     * @todo Implement testAddAutoIndex().
     * @test
     */
    public function addAutoIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::hasAutoIndex
     * @todo Implement testHasAutoIndex().
     * @test
     */
    public function hasAutoIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeAutoIndex
     * @todo Implement testRemoveAutoIndex().
     * @test
     */
    public function removeAutoIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeAllAutoIndexes
     * @todo Implement testRemoveAllAutoIndexes().
     * @test
     */
    public function removeAllAutoIndexes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getMeta
     * @todo Implement testGetMeta().
     * @test
     */
    public function getMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::setMeta
     * @todo Implement testSetMeta().
     * @test
     */
    public function setMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeMeta
     * @todo Implement testRemoveMeta().
     * @test
     */
    public function removeMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getAllMeta
     * @todo Implement testGetAllMeta().
     * @test
     */
    public function getAllMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::removeAllMeta
     * @todo Implement testRemoveAllMeta().
     * @test
     */
    public function removeAllMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::store
     * @todo Implement testStore().
     * @test
     */
    public function store()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::reload
     * @todo Implement testReload().
     * @test
     */
    public function reload()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::delete
     * @todo Implement testDelete().
     * @test
     */
    public function delete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::populate
     * @todo Implement testPopulate().
     * @test
     */
    public function populate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::hasSiblings
     * @todo Implement testHasSiblings().
     * @test
     */
    public function hasSiblings()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getSiblingCount
     * @todo Implement testGetSiblingCount().
     * @test
     */
    public function getSiblingCount()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getSibling
     * @todo Implement testGetSibling().
     * @test
     */
    public function getSibling()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::getSiblings
     * @todo Implement testGetSiblings().
     * @test
     */
    public function getSiblings()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::add
     * @todo Implement testAdd().
     * @test
     */
    public function add()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::link
     * @todo Implement testLink().
     * @test
     */
    public function link()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::map
     * @todo Implement testMap().
     * @test
     */
    public function map()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Object::reduce
     * @todo Implement testReduce().
     * @test
     */
    public function reduce()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
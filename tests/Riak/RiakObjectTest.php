<?php
/**
 * Test class for RiakObject.
 */
class RiakObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RiakObject
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RiakObject(new RiakClient(), new RiakBucket(new RiakClient(), 'test'));
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
     * @covers RiakObject::getBucket
     * @todo Implement testGetBucket().
     */
    public function testGetBucket()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getKey
     * @todo Implement testGetKey().
     */
    public function testGetKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getData
     * @todo Implement testGetData().
     */
    public function testGetData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::setData
     * @todo Implement testSetData().
     */
    public function testSetData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::status
     * @todo Implement testStatus().
     */
    public function testStatus()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::exists
     * @todo Implement testExists().
     */
    public function testExists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getContentType
     * @todo Implement testGetContentType().
     */
    public function testGetContentType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::setContentType
     * @todo Implement testSetContentType().
     */
    public function testSetContentType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getLastModified
     * @todo Implement testGetLastModified().
     */
    public function testGetLastModified()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::addLink
     * @todo Implement testAddLink().
     */
    public function testAddLink()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeLink
     * @todo Implement testRemoveLink().
     */
    public function testRemoveLink()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getLinks
     * @todo Implement testGetLinks().
     */
    public function testGetLinks()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::addIndex
     * @todo Implement testAddIndex().
     */
    public function testAddIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::setIndex
     * @todo Implement testSetIndex().
     */
    public function testSetIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getIndex
     * @todo Implement testGetIndex().
     */
    public function testGetIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeIndex
     * @todo Implement testRemoveIndex().
     */
    public function testRemoveIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeAllIndexes
     * @todo Implement testRemoveAllIndexes().
     */
    public function testRemoveAllIndexes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::addAutoIndex
     * @todo Implement testAddAutoIndex().
     */
    public function testAddAutoIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::hasAutoIndex
     * @todo Implement testHasAutoIndex().
     */
    public function testHasAutoIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeAutoIndex
     * @todo Implement testRemoveAutoIndex().
     */
    public function testRemoveAutoIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeAllAutoIndexes
     * @todo Implement testRemoveAllAutoIndexes().
     */
    public function testRemoveAllAutoIndexes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getMeta
     * @todo Implement testGetMeta().
     */
    public function testGetMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::setMeta
     * @todo Implement testSetMeta().
     */
    public function testSetMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeMeta
     * @todo Implement testRemoveMeta().
     */
    public function testRemoveMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getAllMeta
     * @todo Implement testGetAllMeta().
     */
    public function testGetAllMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::removeAllMeta
     * @todo Implement testRemoveAllMeta().
     */
    public function testRemoveAllMeta()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::store
     * @todo Implement testStore().
     */
    public function testStore()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::reload
     * @todo Implement testReload().
     */
    public function testReload()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::delete
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::populate
     * @todo Implement testPopulate().
     */
    public function testPopulate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::hasSiblings
     * @todo Implement testHasSiblings().
     */
    public function testHasSiblings()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getSiblingCount
     * @todo Implement testGetSiblingCount().
     */
    public function testGetSiblingCount()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getSibling
     * @todo Implement testGetSibling().
     */
    public function testGetSibling()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::getSiblings
     * @todo Implement testGetSiblings().
     */
    public function testGetSiblings()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::add
     * @todo Implement testAdd().
     */
    public function testAdd()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::link
     * @todo Implement testLink().
     */
    public function testLink()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::map
     * @todo Implement testMap().
     */
    public function testMap()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers RiakObject::reduce
     * @todo Implement testReduce().
     */
    public function testReduce()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
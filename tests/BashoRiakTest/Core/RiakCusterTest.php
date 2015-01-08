<?php

namespace BashoRiakTest\Core;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\RiakCluster;

class RiakCusterTest extends TestCase
{
    /**
     * @var \Basho\Riak\RiakConfig
     */
    private $config;

    /**
     * @var \Basho\Riak\Core\RiakCluster
     */
    private $instence;

    protected function setUp()
    {
        parent::setUp();

        $this->config   = $this->getMock('Basho\Riak\RiakConfig', [], [], '', false);
        $this->instence = new RiakCluster($this->config);
    }

    public function testAddAndRemoveNode()
    {
        $node1 = $this->getMock('Basho\Riak\Core\RiakNode', [], [], '', false);
        $node2 = $this->getMock('Basho\Riak\Core\RiakNode', [], [], '', false);

        $this->assertEmpty($this->instence->getNodes());

        $this->instence->addNode($node1);
        $this->instence->addNode($node2);

        $this->assertCount(2, $this->instence->getNodes());
        $this->assertSame($node1, $this->instence->removeNode($node1));
        $this->assertCount(1, $this->instence->getNodes());
        $this->assertSame($node2, $this->instence->removeNode($node2));
        $this->assertEmpty($this->instence->getNodes());
        $this->assertNull($this->instence->removeNode($node1));
    }

    /**
     * @expectedException \Basho\Riak\RiakException
     * @expectedExceptionMessage There are no nodes in the cluster.
     */
    public function testBuildNodeInvalidProtocolException()
    {
        $this->instence->execute($this->getMock('Basho\Riak\Core\RiakOperation'));
    }
}
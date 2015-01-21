<?php

namespace BashoRiakTest\Core\Query;

use BashoRiakTest\TestCase;
use Basho\Riak\Core\Query\BucketProperties;

class BucketPropertiesTest extends TestCase
{
    public function testGetValuesFromMap()
    {
        $props = new BucketProperties( [
            BucketProperties::R               => 1,
            BucketProperties::W               => 2,
            BucketProperties::PR              => 3,
            BucketProperties::PW              => 4,
            BucketProperties::DW              => 5,
            BucketProperties::RW              => 6,
            BucketProperties::N_VAL           => 7,
            BucketProperties::ALLOW_MULT      => true,
            BucketProperties::LAST_WRITE_WINS => false,
            BucketProperties::PRE_COMMIT      => [],
            BucketProperties::POST_COMMIT     => [],
            BucketProperties::OLD_VCLOCK      => 8,
            BucketProperties::YOUNG_VCLOCK    => 9,
            BucketProperties::BIG_VCLOCK      => 10,
            BucketProperties::SMALL_VCLOCK    => 11,
            BucketProperties::BASIC_QUORUM    => true,
            BucketProperties::NOTFOUND_OK     => false,
            BucketProperties::BACKEND         => 'backend',
            BucketProperties::SEARCH          => 'search'
        ]);

        $this->assertEquals(1, $props->getR());
        $this->assertEquals(2, $props->getW());
        $this->assertEquals(3, $props->getPr());
        $this->assertEquals(4, $props->getPw());
        $this->assertEquals(5, $props->getDw());
        $this->assertEquals(6, $props->getRw());
        $this->assertEquals(7, $props->getNVal());
        $this->assertEquals(true, $props->getAllowSiblings());
        $this->assertEquals(false, $props->getLastWriteWins());
        $this->assertEquals([], $props->getPostCommitHooks());
        $this->assertEquals([], $props->getPreCommitHooks());
        $this->assertEquals(8, $props->getOldVClock());
        $this->assertEquals(9, $props->getYoungVClock());
        $this->assertEquals(10, $props->getBigVClock());
        $this->assertEquals(11, $props->getSmallVClock());
        $this->assertEquals(true, $props->getBasicQuorum());
        $this->assertEquals(false, $props->getNotFoundOk());
        $this->assertEquals('backend', $props->getBackend());
        $this->assertEquals('search', $props->getSearch());
    }
}
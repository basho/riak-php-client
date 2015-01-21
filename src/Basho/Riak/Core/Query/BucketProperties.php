<?php

namespace Basho\Riak\Core\Query;

/**
 * Bucket properties used for buckets and bucket types.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class BucketProperties
{
    const R = 'r';
    const W = 'w';
    const PR = 'pr';
    const PW = 'pw';
    const DW = 'dw';
    const RW = 'rw';
    const N_VAL = 'nVal';
    const ALLOW_MULT = 'allowMult';
    const LAST_WRITE_WINS = 'lastWriteWins';
    const PRE_COMMIT = 'preCommit';
    const POST_COMMIT = 'postCommit';
    const OLD_VCLOCK = 'oldVclock';
    const YOUNG_VCLOCK = 'youngVclock';
    const BIG_VCLOCK = 'bigVclock';
    const SMALL_VCLOCK = 'smallVclock';
    const BASIC_QUORUM = 'basicQuorum';
    const NOTFOUND_OK = 'notfoundOk';
    const BACKEND = 'backend';
    const SEARCH = 'search';

    /**
     * @var array
     */
    private $props = [];

    /**
     * @param array $props
     */
    public function __construct(array $props = [])
    {
        $this->props = $props;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    private function get($name, $default = null)
    {
        return isset($this->props[$name])
            ? $this->props[$name]
            : $default;
    }

    /**
     * @return integer
     */
    public function getRw()
    {
        return $this->get(self::RW);
    }

    /**
     * @return integer
     */
    public function getDw()
    {
        return $this->get(self::DW);
    }

    /**
     * @return integer
     */
    public function getW()
    {
        return $this->get(self::W);
    }

    /**
     * @return integer
     */
    public function getR()
    {
        return $this->get(self::R);
    }

    /**
     * @return integer
     */
    public function getPr()
    {
        return $this->get(self::PR);
    }

    /**
     * @return integer
     */
    public function getPw()
    {
        return $this->get(self::PW);
    }

    /**
     * @return boolean
     */
    public function getNotFoundOk()
    {
        return $this->get(self::NOTFOUND_OK);
    }

    /**
     * @return boolean
     */
    public function getBasicQuorum()
    {
        return $this->get(self::BASIC_QUORUM);
    }

    /**
     * @return array
     */
    public function getPreCommitHooks()
    {
        return $this->get(self::POST_COMMIT, []);
    }

    /**
     * @return array
     */
    public function getPostCommitHooks()
    {
        return $this->get(self::POST_COMMIT, []);
    }

    /**
     * @return integer
     */
    public function getOldVClock()
    {
        return $this->get(self::OLD_VCLOCK);
    }

    /**
     * @return integer
     */
    public function getYoungVClock()
    {
        return $this->get(self::YOUNG_VCLOCK);
    }

    /**
     * @return integer
     */
    public function getBigVClock()
    {
        return $this->get(self::BIG_VCLOCK);
    }

    /**
     * @return integer
     */
    public function getSmallVClock()
    {
        return $this->get(self::SMALL_VCLOCK);
    }

    /**
     * @return string
     */
    public function getBackend()
    {
        return $this->get(self::BACKEND);
    }

    /**
     * @return integer
     */
    public function getNVal()
    {
        return $this->get(self::N_VAL);
    }

    /**
     * @return boolean
     */
    public function getLastWriteWins()
    {
        return $this->get(self::LAST_WRITE_WINS);
    }

    /**
     * @return boolean
     */
    public function getAllowSiblings()
    {
        return $this->get(self::ALLOW_MULT);
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->get(self::SEARCH);
    }
}

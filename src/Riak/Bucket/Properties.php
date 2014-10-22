<?php

/*
Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Basho\Riak\Bucket;

/**
 * Class Properties
 *
 *
 *
 * @package     Basho\Riak\Bucket
 * @author      Christopher Mancini <cmancini at basho d0t com>
 * @copyright   2011-2014 Basho Technologies, Inc.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since       2.0
 */
class Properties
{
    /**
     * The number of replicas stored.
     *
     * @var int
     */
    protected $n_val = 3;

    /**
     * Whether or not siblings are allowed.
     *
     * @var bool
     */
    protected $allow_mult = false;

    /**
     * [short description]
     *
     * @var bool
     */
    protected $last_write_wins = false;

    /**
     * Global pre-commit hook
     *
     * @var null
     */
    protected $precommit = null;

    /**
     * Whether or not precommit is set
     *
     * @var bool
     */
    protected $has_precommit = false;

    /**
     * Global post-commit hook
     *
     * @var null
     */
    protected $postcommit = null;

    /**
     * Whether or not postcommit is set
     *
     * @var bool
     */
    protected $has_postcommit = false;

    /**
     * [short description]
     *
     * @var null
     */
    protected $chash_keyfun = null;

    /**
     * [short description]
     *
     * @var null
     */
    protected $linkfun = null;

    /**
     * [short description]
     *
     * @var int
     */
    protected $old_vclock = 0;

    /**
     * [short description]
     *
     * @var int
     */
    protected $young_vclock = 0;

    /**
     * [short description]
     *
     * @var int
     */
    protected $big_vclock = 0;

    /**
     * [short description]
     *
     * @var int
     */
    protected $small_vclock = 0;

    /**
     * Primary read quorum.
     *
     * The number of primary, non-fallback nodes that must return results for a successful GET request.
     *
     * @var int|null
     */
    protected $pr = 0;

    /**
     * Read quorum value.
     *
     * The number of Riak nodes that must return results for a GET request before it is considered successful.
     *
     * @var int|null
     */
    protected $r = null;

    /**
     * Write quorum value.
     *
     * The number of Riak nodes that must accept a PUT request.
     *
     * @var int|null
     */
    protected $w = null;

    /**
     * Primary write quorum.
     *
     * The number of primary, non-fallback nodes that must accept a PUT request. A value of 0 will be interpreted as all
     * and a value of null will be interpreted as 'quorum'.
     *
     * @see http://docs.basho.com/riak/2.0.0/ops/advanced/configs/configuration-files/#-code-riak_core-code-Settings
     * @var int|null
     */
    protected $pw = 0;

    /**
     * Durable write quorum.
     *
     * The number of Riak nodes that have received an acknowledgment of the write from the storage backend.
     *
     * @var int|null
     */
    protected $dw = null;

    /**
     * Delete quorum.
     *
     * @var int|null
     */
    protected $rw = null;

    /**
     * [short description]
     *
     * @var bool
     */
    protected $basic_quorum = false;

    /**
     * [short description]
     *
     * @var bool
     */
    protected $notfound_ok = false;

    /**
     * [short description]
     *
     * @var string
     */
    protected $backend = '';

    /**
     * [short description]
     *
     * @var bool
     */
    protected $search = false;

    /**
     * [short description]
     *
     * @var string
     */
    protected $repl_mode = '';

    /**
     * [short description]
     *
     * @var null
     */
    protected $repl = null;

    /**
     * [short description]
     *
     * @var string
     */
    protected $search_index = '';

    /**
     * [short description]
     *
     * @var string
     */
    protected $datatype = '';

    /**
     * [short description]
     *
     * @var bool
     */
    protected $consistent = false;

    /**
     * @return boolean
     */
    public function isAllowMult()
    {
        return $this->allow_mult;
    }

    /**
     * @param boolean $allow_mult
     */
    public function setAllowMult($allow_mult)
    {
        $this->allow_mult = $allow_mult;
    }

    /**
     * @return string
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @param string $backend
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;
    }

    /**
     * @return boolean
     */
    public function isBasicQuorum()
    {
        return $this->basic_quorum;
    }

    /**
     * @param boolean $basic_quorum
     */
    public function setBasicQuorum($basic_quorum)
    {
        $this->basic_quorum = $basic_quorum;
    }

    /**
     * @return int
     */
    public function getBigVclock()
    {
        return $this->big_vclock;
    }

    /**
     * @param int $big_vclock
     */
    public function setBigVclock($big_vclock)
    {
        $this->big_vclock = $big_vclock;
    }

    /**
     * @return null
     */
    public function getChashKeyfun()
    {
        return $this->chash_keyfun;
    }

    /**
     * @param null $chash_keyfun
     */
    public function setChashKeyfun($chash_keyfun)
    {
        $this->chash_keyfun = $chash_keyfun;
    }

    /**
     * @return boolean
     */
    public function isConsistent()
    {
        return $this->consistent;
    }

    /**
     * @param boolean $consistent
     */
    public function setConsistent($consistent)
    {
        $this->consistent = $consistent;
    }

    /**
     * @return string
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * @param string $datatype
     */
    public function setDatatype($datatype)
    {
        $this->datatype = $datatype;
    }

    /**
     * @return int
     */
    public function getDw()
    {
        return $this->dw;
    }

    /**
     * @param int $dw
     */
    public function setDw($dw)
    {
        $this->dw = $dw;
    }

    /**
     * @return boolean
     */
    public function isHasPostcommit()
    {
        return $this->has_postcommit;
    }

    /**
     * @param boolean $has_postcommit
     */
    public function setHasPostcommit($has_postcommit)
    {
        $this->has_postcommit = $has_postcommit;
    }

    /**
     * @return boolean
     */
    public function isHasPrecommit()
    {
        return $this->has_precommit;
    }

    /**
     * @param boolean $has_precommit
     */
    public function setHasPrecommit($has_precommit)
    {
        $this->has_precommit = $has_precommit;
    }

    /**
     * @return boolean
     */
    public function isLastWriteWins()
    {
        return $this->last_write_wins;
    }

    /**
     * @param boolean $last_write_wins
     */
    public function setLastWriteWins($last_write_wins)
    {
        $this->last_write_wins = $last_write_wins;
    }

    /**
     * @return null
     */
    public function getLinkfun()
    {
        return $this->linkfun;
    }

    /**
     * @param null $linkfun
     */
    public function setLinkfun($linkfun)
    {
        $this->linkfun = $linkfun;
    }

    /**
     * @return int
     */
    public function getNVal()
    {
        return $this->n_val;
    }

    /**
     * @param int $n_val
     */
    public function setNVal($n_val)
    {
        $this->n_val = $n_val;
    }

    /**
     * @return boolean
     */
    public function isNotfoundOk()
    {
        return $this->notfound_ok;
    }

    /**
     * @param boolean $notfound_ok
     */
    public function setNotfoundOk($notfound_ok)
    {
        $this->notfound_ok = $notfound_ok;
    }

    /**
     * @return int
     */
    public function getOldVclock()
    {
        return $this->old_vclock;
    }

    /**
     * @param int $old_vclock
     */
    public function setOldVclock($old_vclock)
    {
        $this->old_vclock = $old_vclock;
    }

    /**
     * @return null
     */
    public function getPostcommit()
    {
        return $this->postcommit;
    }

    /**
     * @param null $postcommit
     */
    public function setPostcommit($postcommit)
    {
        $this->postcommit = $postcommit;
    }

    /**
     * @return int
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * @param int $pr
     */
    public function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     * @return null
     */
    public function getPrecommit()
    {
        return $this->precommit;
    }

    /**
     * @param null $precommit
     */
    public function setPrecommit($precommit)
    {
        $this->precommit = $precommit;
    }

    /**
     * @return int
     */
    public function getPw()
    {
        return $this->pw;
    }

    /**
     * @param int $pw
     */
    public function setPw($pw)
    {
        $this->pw = $pw;
    }

    /**
     * @return int
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * @param int $r
     */
    public function setR($r)
    {
        $this->r = $r;
    }

    /**
     * @return null
     */
    public function getRepl()
    {
        return $this->repl;
    }

    /**
     * @param null $repl
     */
    public function setRepl($repl)
    {
        $this->repl = $repl;
    }

    /**
     * @return string
     */
    public function getReplMode()
    {
        return $this->repl_mode;
    }

    /**
     * @param string $repl_mode
     */
    public function setReplMode($repl_mode)
    {
        $this->repl_mode = $repl_mode;
    }

    /**
     * @return int
     */
    public function getRw()
    {
        return $this->rw;
    }

    /**
     * @param int $rw
     */
    public function setRw($rw)
    {
        $this->rw = $rw;
    }

    /**
     * @return boolean
     */
    public function isSearch()
    {
        return $this->search;
    }

    /**
     * @param boolean $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * @return string
     */
    public function getSearchIndex()
    {
        return $this->search_index;
    }

    /**
     * @param string $search_index
     */
    public function setSearchIndex($search_index)
    {
        $this->search_index = $search_index;
    }

    /**
     * @return int
     */
    public function getSmallVclock()
    {
        return $this->small_vclock;
    }

    /**
     * @param int $small_vclock
     */
    public function setSmallVclock($small_vclock)
    {
        $this->small_vclock = $small_vclock;
    }

    /**
     * @return int
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * @param int $w
     */
    public function setW($w)
    {
        $this->w = $w;
    }

    /**
     * @return int
     */
    public function getYoungVclock()
    {
        return $this->young_vclock;
    }

    /**
     * @param int $young_vclock
     */
    public function setYoungVclock($young_vclock)
    {
        $this->young_vclock = $young_vclock;
    }
}
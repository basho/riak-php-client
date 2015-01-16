<?php

namespace Basho\Riak\Core\Message\Bucket;

use Basho\Riak\Core\Message\Response;

/**
 * This class represents a get response.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class GetResponse extends Response
{
    public $nVal;
    public $allowMult;
    public $lastWriteWins;
    public $preCommitList;
    public $postcommitList;
    public $oldVclock;
    public $youngVclock;
    public $bigVclock;
    public $smallVclock;
    public $pr;
    public $r;
    public $w;
    public $pw;
    public $dw;
    public $rw;
    public $basicQuorum;
    public $notfoundOk;
    public $backend;
    public $search;
}
<?php

namespace Basho\Riak\Core\Message\Kv;

/**
 * This class represents a message content.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class Content
{
    public $value;
    public $vtag;
    public $contentType;
    public $lastModified;
    public $links = [];
    public $metas = [];
    public $indexes = [];
    public $deleted = [];
}

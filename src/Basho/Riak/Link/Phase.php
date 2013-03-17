<?php
/**
 * This file is part of the riak-php-client.
 *
 * PHP version 5.3+
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link https://github.com/localgod/riak-php-client
 */
namespace Basho\Riak\Link;
/**
 * The Phase object holds information about a Link phase in a
 * map/reduce operation.
 */
class Phase
{
    /**
     * The name of the bucket
     * @var string
     */
    private $bucket;

    /**
     * The tag
     * @var string|null
     */
    private $tag;

    /**
     * Should we return results of current phase.
     * @var boolean
     */
    private $keep;

    /**
     * Construct a Phase object.
     *
     * @param string  $bucket The bucket name.
     * @param string  $tag    The tag.
     * @param boolean $keep   True to return results of this phase.
     *
     * @return void
     */
    public function __construct($bucket, $tag, $keep)
    {
        $this->bucket = $bucket;
        $this->tag = $tag;
        $this->keep = $keep;
    }
    /**
     * Return results of current phase?
     *
     * @return boolean
     */
    public function getKeep()
    {
        return $this->keep;
    }

    /**
     * Return results of current phase?
     *
     * @param boolean $keep The keep value
     *
     * @return \Basho\Riak\Link\Phase
     */
    public function setKeep($keep)
    {
        $this->keep = $keep;
        return $this;
    }

    /**
     * Convert the Phase to an associative array.
     *
     * @internal Used internally.
     * @return array
     */
    public function toArray()
    {
        $stepdef = array("bucket" => $this->bucket, "tag" => $this->tag,
                "keep" => $this->keep);

        return array("link" => $stepdef);
    }
}

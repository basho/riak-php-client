<?php
/**
 * This file is part of the riak-php-client.
 *
 * PHP version 5.3+
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link https://github.com/localgod/riak-php-client
 */
namespace Basho\Riak;

/**
 * The LinkPhase object holds information about a Link phase in a
 * map/reduce operation.
 */
class LinkPhase
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
    public $keep;

    /**
     * Construct a LinkPhase object.
     *
     * @param string  $bucket The bucket name.
     * @param string  $tag    The tag.
     * @param boolean $keep   True to return results of this phase.
     */
    public function __construct($bucket, $tag, $keep)
    {
        $this->bucket = $bucket;
        $this->tag = $tag;
        $this->keep = $keep;
    }

    /**
     * This method is only here to maintain backwards compatibility
     * with old method names pre PSR coding standard
     *
     * @param string $name      Name of old method
     * @param array  $arguments Arguments for method
     *
     * @return void
     */
    public function __call($name, $arguments)
    {
        if ($name == 'to_array') {
            self::toArray();
        }
    }

    /**
     * Convert the LinkPhase to an associative array. Used
     * internally.
     *
     * @return array
     */
    public function toArray()
    {
        $stepdef = array("bucket" => $this->bucket, "tag" => $this->tag,
                "keep" => $this->keep);

        return array("link" => $stepdef);
    }
}

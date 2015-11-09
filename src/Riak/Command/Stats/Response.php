<?php

namespace Basho\Riak\Command\Stats;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    protected $stats = [];

    public function __construct($statusCode, $headers = [], $body = '')
    {
        parent::__construct($statusCode, $headers, $body);

        $this->stats = json_decode($body, true);
    }

    public function __get($name) {
        if (isset($this->stats[$name])) {
            return $this->stats[$name];
        }

        return null;
    }

    public function getAllStats() {
        return $this->stats;
    }
}

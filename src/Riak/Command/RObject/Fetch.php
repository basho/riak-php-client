<?php

namespace Basho\Riak\Command\RObject;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Fetches a Riak Kv RObject
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command\RObject implements CommandInterface
{
    public function __construct(Command\Builder\FetchObject $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
        $this->decodeAsAssociative = $builder->getDecodeAsAssociative();
    }
}

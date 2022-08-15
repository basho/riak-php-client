<?php

namespace Basho\Riak\Command\RObject;

use Basho\Riak\Command;

use Basho\Riak\CommandInterface;

/**
 * Fetches the Preflist for a Riak Kv RObject
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchPreflist extends Command\RObject implements CommandInterface
{
    public function __construct(Command\Builder\FetchPreflist $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }
}

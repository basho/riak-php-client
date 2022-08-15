<?php

namespace Basho\Riak\Command\RObject;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to remove an object from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Delete extends Command\RObject implements CommandInterface
{
    protected $method = 'DELETE';

    public function __construct(Command\Builder\DeleteObject $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }
}

<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Used to ping Riak and make sure the ring is available
 *
 * <code>
 * $command = (new Command\Builder\Ping($riak))
 *   ->build();
 *
 * $response = $command->execute($command);
 *
 * if ($response->isSuccess()) {
 *   echo 'YAY!';
 * }
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Ping extends Command\Builder implements Command\BuilderInterface
{
    public function __construct(Riak $riak)
    {
        parent::__construct($riak);
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Ping;
     */
    public function build()
    {
        $this->validate();

        return new Command\Ping($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
    }
}

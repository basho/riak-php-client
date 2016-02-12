<?php

namespace Basho\Riak\Command\TimeSeries;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to store data within a TS table
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * Stores the table name
     *
     * @var string|null
     */
    protected $table = NULL;

    protected $key = null;

    public function getData()
    {
        return ["key" => $this->key];
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function __construct(Command\Builder\TimeSeries\FetchRow $builder)
    {
        parent::__construct($builder);

        $this->table = $builder->getTable();
        $this->key = $builder->getKey();
    }
}
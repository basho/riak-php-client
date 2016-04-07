<?php

namespace Basho\Riak\Command\TimeSeries;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to store data within a TS table
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'POST';

    /**
     * Stores the table name
     *
     * @var string|null
     */
    protected $table = NULL;

    /**
     * Stores the rows
     *
     * @var array $rows
     */
    protected $rows = [];

    public function getTable()
    {
        return $this->table;
    }

    public function getData()
    {
        return $this->rows;
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function __construct(Command\Builder\TimeSeries\StoreRows $builder)
    {
        parent::__construct($builder);

        $this->table = $builder->getTable();
        $this->rows = $builder->getRows();
    }
}
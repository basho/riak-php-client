<?php

namespace Basho\Riak\Command\TimeSeries;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to store data within a TS table
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Delete extends Command implements CommandInterface
{
    protected $method = 'DELETE';

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

    public function getData()
    {
        return $this->rows;
    }

    public function getEncodedData()
    {
        return $this->getData();
    }

    public function __construct(Command\Builder\TimeSeries\DeleteRow $builder)
    {
        parent::__construct($builder);

        $this->table = $builder->getTable();
        $this->rows = $builder->getRows();
    }
}
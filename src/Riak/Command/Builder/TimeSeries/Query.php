<?php

namespace Basho\Riak\Command\Builder\TimeSeries;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Query extends Command\Builder implements Command\BuilderInterface
{
    protected $query = '';

    /**
     * TimeSeries SQL'ish query
     *
     * @param $query
     */
    public function withQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\TimeSeries\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\TimeSeries\Query\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Query');
    }
}

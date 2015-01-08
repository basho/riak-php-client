<?php

namespace Basho\Riak\Core\Converter;

use Basho\Riak\Core\Converter\RiakObjectReference;
use Basho\Riak\Core\Converter\DomainObjectReference;

/**
 * The Converter acts as a bridge between the core and the user level API.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Converter
{
    /**
     * Convert from a riak objet object reference to a domain object reference.
     *
     * @param \Basho\Riak\Core\Converter\DomainObjectReference $reference
     *
     * @return object
     */
    public function toDomain(RiakObjectReference $reference);

    /**
     * Convert from a domain object reference to a riak objet object reference.
     *
     * @param \Basho\Riak\Core\Converter\RiakObjectReference $reference
     *
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    public function fromDomain(DomainObjectReference $reference);
}

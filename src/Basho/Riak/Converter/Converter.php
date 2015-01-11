<?php

namespace Basho\Riak\Converter;


/**
 * The Converter acts as a bridge between the core and the user level API.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
interface Converter
{
    /**
     * Convert from a riak objet object reference to a domain object reference.
     *
     * @param \Basho\Riak\Converter\DomainObjectReference $reference
     *
     * @return object
     */
    public function toDomain(RiakObjectReference $reference);

    /**
     * Convert from a domain object reference to a riak objet object reference.
     *
     * @param \Basho\Riak\Converter\RiakObjectReference $reference
     *
     * @return \Basho\Riak\Core\Query\RiakObject
     */
    public function fromDomain(DomainObjectReference $reference);
}

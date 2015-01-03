<?php

namespace Basho\Riak\Core\Converter;

use Basho\Riak\Core\Converter\Hydrator\DomainHydrator;

/**
 * The default Converter used when storing and fetching domain objects from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class JsonConverter extends BaseConverter
{
    /**
     * @param \Basho\Riak\Core\Converter\Hydrator\DomainHydrator $domainHydrator
     */
    public function __construct(DomainHydrator $domainHydrator)
    {
        parent::__construct($domainHydrator);
    }

    /**
     * {@inheritdoc}
     */
    protected function fromDomainObject($domainObject)
    {
        return json_encode($domainObject);
    }

    /**
     * {@inheritdoc}
     */
    protected function toDomainObject($value, $type)
    {
        $data = json_decode($value, true);

        if ( ! is_array($data)) {
            return new $type($data);
        }

        $object = new $type();

        foreach ($data as $key => $value) {
            call_user_func([$object , 'set'. ucfirst($key)], $value);
        }

        return $object;
    }
}

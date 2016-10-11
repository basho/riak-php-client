<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Api\Http;
use Basho\Riak\Object as RObject;

/**
 * Allows easy code sharing for Object getters / setters within the Command Builders
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait ObjectTrait
{
    /**
     * @var \Basho\Riak\Object|null
     */
    protected $object = NULL;

    /**
     * @return Object|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Mint a new Object instance with supplied params and attach it to the Command
     *
     * @param string $data
     * @param array $headers
     *
     * @return $this
     */
    public function buildObject($data = NULL, $headers = NULL)
    {
        $this->object = new RObject($data, $headers);

        return $this;
    }

    /**
     * Attach an already instantiated Object to the Command
     *
     * @param \Basho\Riak\Object $object
     *
     * @return $this
     */
    public function withObject(RObject $object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Mint a new Object instance with a json encoded string
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function buildJsonObject($data)
    {
        $this->object = new RObject();
        $this->object->setData($data);
        $this->object->setContentType(Http::CONTENT_TYPE_JSON);

        return $this;
    }
}

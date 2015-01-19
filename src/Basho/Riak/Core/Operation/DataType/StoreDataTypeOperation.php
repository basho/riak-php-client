<?php

namespace Basho\Riak\Core\Operation\DataType;

use Basho\Riak\Converter\CrdtResponseConverter;
use Basho\Riak\Core\Message\DataType\PutRequest;
use Basho\Riak\Core\Query\Crdt\Op\CrdtOp;
use Basho\Riak\Core\Query\Crdt\DataType;
use Basho\Riak\Core\Query\RiakLocation;
use Basho\Riak\Core\RiakOperation;
use Basho\Riak\Core\RiakAdapter;

/**
 * An operation used to store a datatype in Riak.
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
abstract class StoreDataTypeOperation implements RiakOperation
{
    /**
     * @var \Basho\Riak\Converter\CrdtResponseConverter
     */
    protected $converter;

    /**
     * @var \Basho\Riak\Core\Query\RiakLocation
     */
    protected $location;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var \Basho\Riak\Core\Query\Crdt\Op\CrdtOp
     */
    protected $op;

    /**
     * @param \Basho\Riak\Converter\CrdtResponseConverter $converter
     * @param \Basho\Riak\Core\Query\RiakLocation         $location
     * @param \Basho\Riak\Core\Query\Crdt\Op\CrdtOp       $op
     * @param array                                       $options
     */
    public function __construct(CrdtResponseConverter $converter, RiakLocation $location, CrdtOp $op, array $options)
    {
        $this->converter = $converter;
        $this->location  = $location;
        $this->options   = $options;
        $this->op        = $op;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakAdapter $adapter)
    {
        $putRequest  = $this->createGetRequest();
        $putResponse = $adapter->send($putRequest);
        $datatype    = $this->converter->convertCounter($putResponse);
        $response    = $this->createDataTypeResponse($datatype);

        return $response;
    }

    /**
     * @return \Basho\Riak\Core\Message\DataType\PutRequest
     */
    private function createGetRequest()
    {
        $request   = new PutRequest();
        $namespace = $this->location->getNamespace();

        $request->type   = $namespace->getBucketType();
        $request->bucket = $namespace->getBucketName();
        $request->key    = $this->location->getKey();
        $request->op     = $this->op;

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }

    /**
     * @param \Basho\Riak\Core\Query\Crdt\DataType $datatype
     *
     * @return \Basho\Riak\Command\DataType\Response\Response
     */
    abstract protected function createDataTypeResponse(DataType $datatype = null);
}

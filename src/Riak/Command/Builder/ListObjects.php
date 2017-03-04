<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Used to list KV objects in Riak
 *
 * <code>
 * $command = (new Command\Builder\ListObjecst($riak))
 *   ->buildBucket('users', 'default')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $key = $response->getObject();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ListObjects extends Command\Builder implements Command\BuilderInterface
{
    use BucketTrait;
    use ObjectTrait;

    /**
     * @var bool
     */
    protected $decodeAsAssociative = false;

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Object\Keys
     */
    public function build()
    {
        $this->validate();

        return new Command\Object\Keys($this);
    }

    /**
     * Tells the client to decode the data as an associative array instead of a PHP stdClass object.
     * Only works if the fetched object type is JSON.
     *
     * @return $this
     */
    public function withDecodeAsAssociative()
    {
        $this->decodeAsAssociative = true;
        return $this;
    }

    /**
     * Fetch the setting for decodeAsAssociative.
     *
     * @return bool
     */
    public function getDecodeAsAssociative()
    {
        return $this->decodeAsAssociative;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');
    }
}

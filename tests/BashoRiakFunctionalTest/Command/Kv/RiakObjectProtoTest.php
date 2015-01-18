<?php

namespace BashoRiakFunctionalTest\Command\Kv;

/**
 * @group proto
 * @group functional
 */
class RiakObjectProtoTest extends RiakObjectTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}
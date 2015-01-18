<?php

namespace BashoRiakFunctionalTest\Command\Kv;

/**
 * @group proto
 * @group functional
 */
class RiakUserMetaProtoTest extends RiakUserMetaTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}
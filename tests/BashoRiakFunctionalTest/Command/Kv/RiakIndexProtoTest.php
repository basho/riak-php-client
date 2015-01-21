<?php

namespace BashoRiakFunctionalTest\Command\Kv;

/**
 * @group proto
 * @group functional
 */
class RiakIndexProtoTest extends RiakIndexTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}
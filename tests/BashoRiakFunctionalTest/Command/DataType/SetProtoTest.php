<?php

namespace BashoRiakFunctionalTest\Command\DataType;

/**
 * @group proto
 * @group functional
 */
class SetProtoTest extends SetTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}
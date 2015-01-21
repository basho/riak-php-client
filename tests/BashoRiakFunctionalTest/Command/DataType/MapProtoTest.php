<?php

namespace BashoRiakFunctionalTest\Command\DataType;

/**
 * @group proto
 * @group functional
 */
class MapProtoTest extends MapTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}
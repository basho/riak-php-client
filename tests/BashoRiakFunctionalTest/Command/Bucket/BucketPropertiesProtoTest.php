<?php

namespace BashoRiakFunctionalTest\Command\Bucket;

/**
 * @group proto
 * @group functional
 */
class BucketPropertiesProtoTest extends BucketPropertiesTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}
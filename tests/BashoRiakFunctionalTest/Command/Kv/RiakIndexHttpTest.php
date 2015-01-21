<?php

namespace BashoRiakFunctionalTest\Command\Kv;

/**
 * @group http
 * @group functional
 */
class RiakIndexHttpTest extends RiakIndexTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}
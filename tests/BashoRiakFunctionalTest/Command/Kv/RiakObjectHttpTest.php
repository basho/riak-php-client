<?php

namespace BashoRiakFunctionalTest\Command\Kv;

/**
 * @group http
 * @group functional
 */
class RiakObjectHttpTest extends RiakObjectTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}
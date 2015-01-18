<?php

namespace BashoRiakFunctionalTest\Command\DataType;

/**
 * @group http
 * @group functional
 */
class SetHttpTest extends SetTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}
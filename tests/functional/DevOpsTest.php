<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Functional tests to perform devops tasks with Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class DevOpsTest extends TestCase
{
    /**
     * @dataProvider getLocalNodeConnection
     * @param $riak \Basho\Riak
     */
    public function testPing($riak)
    {
        // build an object
        $command = (new Command\Builder\Ping($riak))
            ->build();

        $response = $command->execute();

        $this->assertTrue($response->isSuccess());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('OK', $response->getBody());
    }

    /**
     * @dataProvider getLocalNodeConnection
     * @param $riak \Basho\Riak
     */
    public function testStats($riak)
    {
        // build an object
        $command = (new Command\Builder\FetchStats($riak))
            ->build();

        /** @var $response \Basho\Riak\Command\Stats\Response */
        $response = $command->execute();

        $this->assertTrue($response->isSuccess());
        $this->assertNotEmpty($response->getBody());
        $this->assertNotEmpty($response->getAllStats());
    }
}

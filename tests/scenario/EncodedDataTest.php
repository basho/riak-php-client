<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Scenario tests for when storing / retrieving binary data
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class EncodedDataTest extends TestCase
{
    const TEST_IMG = "Basho_Man_Super.png";
    const BUCKET = "encodeddata";
    const BASE64_KEY = "base64";
    const BINARY_KEY = "binary.png";
    const TEST_CONTENT_TYPE = "image/png";

    public function testBase64EncodedData() {
        $image = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . static::TEST_IMG);

        $object = (new Riak\Object($image))->setContentEncoding("base64")->setContentType(static::TEST_CONTENT_TYPE);

        $storeCommand = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::BASE64_KEY, static::BUCKET)
            ->build();

        $response = $storeCommand->execute();

        $this->assertEquals('204', $response->getCode());

        $fetchCommand = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::BASE64_KEY, static::BUCKET)
            ->build();

        $response = $fetchCommand->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals(static::TEST_CONTENT_TYPE, $response->getObject()->getContentType());
        $this->assertEquals(base64_encode($image), $storeCommand->getEncodedData());
        $this->assertEquals($storeCommand->getEncodedData(), $response->getObject()->getData());
        $this->assertEquals(md5($storeCommand->getEncodedData()), md5($response->getObject()->getData()));
    }

    public function testNoEncoding() {
        $image = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . static::TEST_IMG);

        $object = (new Riak\Object('<img src="data:image/png;base64,' . base64_encode($image) . '"/>'))->setContentEncoding("none")->setContentType("text/html");

        $storeCommand = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::BASE64_KEY . '.png', static::BUCKET)
            ->build();

        $response = $storeCommand->execute();

        $this->assertEquals('204', $response->getCode());

        $fetchCommand = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::BASE64_KEY . '.png', static::BUCKET)
            ->build();

        $response = $fetchCommand->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals($object->getData(), $response->getObject()->getData());
    }

    public function testBinaryData() {
        $image = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . static::TEST_IMG);
        $md5 = md5($image);

        $object = (new Riak\Object($image))->setContentEncoding("binary")->setContentType(static::TEST_CONTENT_TYPE);

        $storeCommand = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::BINARY_KEY, static::BUCKET)
            ->build();

        $response = $storeCommand->execute();

        $this->assertEquals('204', $response->getCode());
        $this->assertEquals($md5, md5($storeCommand->getEncodedData()));

        $fetchCommand = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::BINARY_KEY, static::BUCKET)
            ->build();

        $response = $fetchCommand->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\Object', $response->getObject());
        $this->assertEquals(static::TEST_CONTENT_TYPE, $response->getObject()->getContentType());
        $this->assertEquals($md5, md5($response->getObject()->getData()));
    }
}

<?php

namespace Basho\Riak\Core\Adapter\Proto;

use Basho\Riak\RiakException;
use DrSlump\Protobuf\Message;
use DrSlump\Protobuf\Protobuf;
use Basho\Riak\ProtoBuf\RiakMessageCodes;

/**
 * RPB socket connection
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class ProtoClient
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * Mapping of message code to PB response class names
     *
     * @var array
     */
    private static $respClassMap = array(
        RiakMessageCodes::DT_FETCH_RESP         => 'Basho\Riak\ProtoBuf\DtFetchResp',
        RiakMessageCodes::DT_UPDATE_RESP        => 'Basho\Riak\ProtoBuf\DtUpdateResp',
        RiakMessageCodes::ERROR_RESP            => 'Basho\Riak\ProtoBuf\RpbErrorResp',
        RiakMessageCodes::GET_BUCKET_RESP       => 'Basho\Riak\ProtoBuf\RpbGetBucketResp',
        RiakMessageCodes::GET_RESP              => 'Basho\Riak\ProtoBuf\RpbGetResp',
        RiakMessageCodes::GET_SERVER_INFO_RESP  => 'Basho\Riak\ProtoBuf\RpbGetServerInfoResp',
        RiakMessageCodes::LIST_BUCKETS_RESP     => 'Basho\Riak\ProtoBuf\RpbListBucketsResp',
        RiakMessageCodes::LIST_KEYS_RESP        => 'Basho\Riak\ProtoBuf\RpbListKeysResp',
        RiakMessageCodes::PUT_RESP              => 'Basho\Riak\ProtoBuf\RpbPutResp',
    );

    /**
     * @param string $host
     * @param string $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param integer $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param \DrSlump\Protobuf\Message $message
     * @param integer                   $messageCode
     * @param integer                   $expectedResponseCode
     *
     * @return \DrSlump\Protobuf\Message
     */
    public function send(Message $message, $messageCode, $expectedResponseCode)
    {
        $payload  = $this->encodeMessage($message, $messageCode);
        $class    = $this->classForCode($expectedResponseCode);
        $response = $this->sendData($payload);
        $respCode = $response[0];
        $respBody = $response[1];

        if ($respCode != $expectedResponseCode) {
            $this->throwResponseException($respCode, $respBody);
        }

        if ($class == null) {
            return;
        }

        return Protobuf::decode($class, $respBody);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function classForCode($code)
    {
        if (isset(self::$respClassMap[$code])) {
            return self::$respClassMap[$code];
        }

        return null;
    }

    /**
     * @param integer $actualCode
     * @param string  $respBody
     *
     * @throws \Basho\Riak\RiakException
     */
    protected function throwResponseException($actualCode, $respBody)
    {
        $this->resource = null;

        $exceptionCode    = $actualCode;
        $exceptionMessage = "Unexpected rpb response code: " . $actualCode;

        if ($actualCode === RiakMessageCodes::ERROR_RESP) {
            $errorClass   = self::$respClassMap[$actualCode];
            $errorMessage = Protobuf::decode($errorClass, $respBody);

            if ($errorMessage->hasErrmsg()) {
                $exceptionMessage  = $errorMessage->getErrmsg();
            }

            if ($errorMessage->hasErrcode()) {
                $exceptionCode = $errorMessage->getErrcode();
            }
        }

        throw new RiakException($exceptionMessage, $exceptionCode);
    }

    /**
     * @return resource
     */
    protected function getConnection()
    {
        if ($this->resource != null && is_resource($this->resource)) {
            return $this->resource;
        }

        $errno    = null;
        $errstr   = null;
        $uri      = sprintf('tcp://%s:%s', $this->host, $this->port);
        $resource = stream_socket_client($uri, $errno, $errstr);

        if ( ! is_resource($resource)) {
            throw new RiakException(sprintf('Fail to connect to : %s [%s %s]', $uri, $errno, $errstr));
        }

        if ($this->timeout !== null) {
            stream_set_timeout($resource, $this->timeout);
        }

        return $this->resource = $resource;
    }

    /**
     * @param \DrSlump\Protobuf\Message $message
     * @param integer                   $code
     *
     * @return string
     */
    private function encodeMessage(Message $message, $code)
    {
        $encoded = Protobuf::encode($message);
        $lenght  = strlen($encoded);

        return pack("NC", 1 + $lenght, $code) . $encoded;
    }

    /**
     * @param string $payload
     *
     * @return array
     *
     * @throws \Exception
     */
    private function sendData($payload)
    {
        $resource = $this->getConnection();
        $lenght   = strlen($payload);
        $fwrite   = 0;

        for ($written = 0; $written < $lenght; $written += $fwrite) {
            $fwrite = fwrite($resource, substr($payload, $written));

            if ($fwrite === false) {
                throw new RiakException('Failed to write message');
            }
        }

        return $this->receive();
    }

    /**
     * @return array
     */
    private function receive()
    {
        $message  = '';
        $resource = $this->getConnection();
        $header   = fread($resource, 4);

        if ($header === false) {
            throw new RiakException('Fail to read response headers');
        }

        if (strlen($header) !== 4) {
            throw new RiakException('Short read on header, read ' . strlen($header) . ' bytes');
        }

        $unpackHeaders = array_values(unpack("N", $header));
        $length        = isset($unpackHeaders[0]) ? $unpackHeaders[0] : 0;

        while (strlen($message) !== $length) {

            $buffer = fread($resource, min(8192, $length - strlen($message)));

            if ( ! strlen($buffer) || $buffer === false) {
                throw new RiakException('Fail to read socket response');
            }

            $message .= $buffer;
        }

        $messageBodyString = substr($message, 1);
        $messageCodeString = substr($message, 0, 1);
        list($messageCode) = array_values(unpack("C", $messageCodeString));

        return [$messageCode, $messageBodyString];
    }
}
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
     * Mapping of message code to PB class names
     *
     * @var array
     */
    private static $classMap = array(
        RiakMessageCodes::MSG_ERRORRESP         => 'Basho\Riak\ProtoBuf\RpbErrorResp',
        RiakMessageCodes::MSG_GETSERVERINFORESP => 'Basho\Riak\ProtoBuf\RpbGetServerInfoResp',
        RiakMessageCodes::MSG_GETREQ            => 'Basho\Riak\ProtoBuf\RpbGetReq',
        RiakMessageCodes::MSG_GETRESP           => 'Basho\Riak\ProtoBuf\RpbGetResp',
        RiakMessageCodes::MSG_PUTREQ            => 'Basho\Riak\ProtoBuf\RpbPutReq',
        RiakMessageCodes::MSG_PUTRESP           => 'Basho\Riak\ProtoBuf\RpbPutResp',
        RiakMessageCodes::MSG_DELREQ            => 'Basho\Riak\ProtoBuf\RpbDelReq',
        RiakMessageCodes::MSG_LISTBUCKETSREQ    => 'Basho\Riak\ProtoBuf\RpbListBucketsReq',
        RiakMessageCodes::MSG_LISTBUCKETSRESP   => 'Basho\Riak\ProtoBuf\RpbListBucketsResp',
        RiakMessageCodes::MSG_LISTKEYSREQ       => 'Basho\Riak\ProtoBuf\RpbListKeysReq',
        RiakMessageCodes::MSG_LISTKEYSRESP      => 'Basho\Riak\ProtoBuf\RpbListKeysResp',
        RiakMessageCodes::MSG_GETBUCKETREQ      => 'Basho\Riak\ProtoBuf\RpbListKeysResp',
        RiakMessageCodes::MSG_GETBUCKETRESP     => 'Basho\Riak\ProtoBuf\RpbGetBucketResp',
        RiakMessageCodes::MSG_DTFETCHRESP       => 'Basho\Riak\ProtoBuf\DtFetchResp',
        RiakMessageCodes::MSG_DTUPDATERESP      => 'Basho\Riak\ProtoBuf\DtUpdateResp',
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
        if (isset(self::$classMap[$code])) {
            return self::$classMap[$code];
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

        if ($actualCode !== RiakMessageCodes::MSG_ERRORRESP) {
            throw new RiakException("Unexpected rpb response code: " . $actualCode);
        }

        $errorClass   = self::$classMap[$actualCode];
        $errorMessage = Protobuf::decode($errorClass, $respBody);

        if ($errorMessage->hasErrmsg()) {
            throw new RiakException($errorMessage->getErrmsg(), $actualCode);
        }
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

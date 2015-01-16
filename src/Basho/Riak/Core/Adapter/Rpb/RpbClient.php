<?php

namespace Basho\Riak\Core\Adapter\Rpb;

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
class RpbClient
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
        RiakMessageCodes::MSG_GETBUCKETRESP     => 'Basho\Riak\ProtoBuf\RpbGetBucketReq',
        RiakMessageCodes::MSG_SETBUCKETREQ      => 'Basho\Riak\ProtoBuf\RpbGetBucketResp',
        RiakMessageCodes::MSG_SETBUCKETRESP     => 'Basho\Riak\ProtoBuf\RpbSetBucketReq'
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
     * @param \DrSlump\Protobuf\Message $message
     * @param integer                   $messageCode
     * @param integer                   $expectedResponseCode
     *
     * @return \DrSlump\Protobuf\Message
     */
    public function send(Message $message, $messageCode, $expectedResponseCode)
    {
        $payload        = $this->encodeMessage($message, $messageCode);
        $responseClass  = $this->classForCode($expectedResponseCode);

        $this->sendData($payload);

        if ($responseClass == null) {
            $this->receive($expectedResponseCode);

            return;
        }

        return $this->receiveMessage($responseClass, $expectedResponseCode);
    }

    /**
     * @param  string $code
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function classForCode($code)
    {
        if (isset(self::$classMap[$code])) {
            return self::$classMap[$code];
        }

        return null;
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
        $resource = stream_socket_client($uri, $errno, $errstr, 30);

        if ( ! is_resource($resource)) {
            throw new \Exception('Error creating socket. Error number :' . $errno . ' error string: '. $errstr);
        }

        stream_set_timeout($resource, 86400);

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
                throw new \Exception('Failed to write message');
            }
        }
    }

    /**
     * @param integer $expectedCode
     *
     * @return array
     */
    private function receive($expectedCode)
    {
        $message  = '';
        $resource = $this->getConnection();
        $header   = fread($resource, 4);

        if ($header === false) {
            throw new \Exception('Fail to read response headers');
        }

        if (strlen($header) !== 4) {
            throw new \Exception('Short read on header, read ' . strlen($header) . ' bytes');
        }

        $unpackHeaders = array_values(unpack("N", $header));
        $length        = isset($unpackHeaders[0]) ? $unpackHeaders[0] : 0;

        while (strlen($message) !== $length) {

            $buffer = fread($resource, min(8192, $length - strlen($message)));

            if ( ! strlen($buffer) || $buffer === false) {
                throw new \Exception('Fail to read socket response');
            }

            $message .= $buffer;
        }

        $messageBodyString = substr($message, 1);
        $messageCodeString = substr($message, 0, 1);
        list($messageCode) = array_values(unpack("C", $messageCodeString));

        return [$messageCode, $messageBodyString];
    }

    /**
     * @param string  $class
     * @param integer $expectedCode
     *
     * @return \DrSlump\Protobuf\Message
     */
    private function receiveMessage($class, $expectedCode)
    {
        $response = $this->receive($expectedCode);
        $message  = Protobuf::decode($class, $response[1]);

        return $message;
    }
}

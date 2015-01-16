<?php

namespace Basho\Riak\Core\Adapter\Rpb;

use DrSlump\Protobuf\Message;
use DrSlump\Protobuf\Protobuf;

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
     * @param integer                   $code
     * @param string                    $responseClass
     *
     * @return \DrSlump\Protobuf\Message
     */
    public function send(Message $message, $code, $responseClass = null)
    {
        $this->sendData($this->encodeMessage($message, $code));

        if ($responseClass == null) {
            return;
        }

        return $this->receiveMessage($responseClass);
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
     * @param string $class
     *
     * @return \DrSlump\Protobuf\Message
     */
    private function receiveMessage($class)
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

        list($length) = array_values(unpack("N", $header));

        while (strlen($message) !== $length) {

            $buffer = fread($resource, min(8192, $length - strlen($message)));

            if ( ! strlen($buffer) || $buffer === false) {
                throw new \Exception('Fail to read socket response');
            }

            $message .= $buffer;
        }

        // $messageCodeString = substr($message, 0, 1);
        // list($messageCode) = array_values(unpack("C", $messageCodeString));

        return Protobuf::decode($class, substr($message, 1));
    }
}

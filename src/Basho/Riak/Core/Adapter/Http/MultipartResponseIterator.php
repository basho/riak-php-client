<?php

namespace Basho\Riak\Core\Adapter\Http;

use Iterator;
use InvalidArgumentException;
use GuzzleHttp\Message\MessageParser;
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Message\MessageFactoryInterface;

/**
 * Multipart stream parser iterator
 *
 * @author    Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @copyright 2011-2015 Basho Technologies, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0 License
 * @since     2.0
 */
class MultipartResponseIterator implements Iterator
{
    /**
     * @var \GuzzleHttp\Message\ResponseInterface
     */
    private $response;

    /**
     * @var \Basho\Riak\Core\Adapter\Http\MultipartStreamIterator
     */
    private $iterator;

    /**
     * @var \GuzzleHttp\Message\MessageParser
     */
    private $parser;

    /**
     * @var \GuzzleHttp\Message\Response
     */
    private $current;

    /**
     * @var \GuzzleHttp\Message\MessageFactoryInterface
     */
    private $factory;

    /**
     * @param \GuzzleHttp\Message\ResponseInterface       $response
     * @param \GuzzleHttp\Message\MessageParser           $parser
     * @param \GuzzleHttp\Message\MessageFactoryInterface $messageFactory
     */
    public function __construct(ResponseInterface $response, MessageParser $parser = null, MessageFactoryInterface $messageFactory = null)
    {
        $matches = null;
        $body    = $response->getBody();
        $header  = $response->getHeader('Content-Type');

        if ( ! preg_match('/boundary=(.*)$/', $header, $matches) || ! isset($matches[1])) {
            throw new InvalidArgumentException("Unable to parse multipart content [Content-Type : $header]");
        }

        $this->response = $response;
        $this->parser   = $parser ?: new MessageParser();
        $this->factory  = $messageFactory ?: new MessageFactory();
        $this->iterator = new MultipartStreamIterator($body, $matches[1]);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $message  = $this->iterator->current();
        $code     = $this->response->getStatusCode();
        $content  = sprintf("HTTP/1.1 300\r\n %s", $message);
        $element  = $this->parser->parseResponse($content);
        $current  = $this->factory->createResponse($code, $element['headers'], $element['body']);

        $this->current = $current;

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->current = null;

        return $this->iterator->rewind();
    }
}

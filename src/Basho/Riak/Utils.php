<?php
/**
 * Riak PHP Client
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Apache License, Version 2.0 that is
 * bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to <eng@basho.com> so we can send you a copy immediately.
 *
 * @category   Basho
 * @copyright  Copyright (c) 2013 Basho Technologies, Inc. and contributors.
 */
namespace Basho\Riak;

use Basho\Riak\Bucket,
    Basho\Riak\Riak,
    Basho\Riak\StringIO;

/**
 * Utils
 *
 * @category   Basho
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class Utils
{

    /**
     * Fetch a value from an array by it's key, or default if not exists
     *
     * @param mixed $key Key to fetch
     * @param array $array Array to fetch from
     * @param mixed $defaultValue Default to return if not exists
     *
     * @return mixed
     */
    public static function get_value($key, $array, $defaultValue)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $defaultValue;
        }
    }

    /**
     * Construct REST URLs
     *
     * Given a Client, Bucket, Key, LinkSpec, and Params,
     * construct and return a URL.
     *
     * @param \Basho\Riak\Riak
     * @param \Basho\Riak\Bucket $bucket
     * @param string $key
     * @param string $spec
     * @param string $params Array of key-value param pairs
     * @return string
     */
    public static function buildRestPath($client, $bucket = null, $key = null, $spec = null, $params = null)
    {
        # Build 'http://hostname:port/prefix/bucket'
        $path = 'http://';
        $path .= $client->host . ':' . $client->port;
        $path .= '/' . $client->prefix;

        # Add '.../bucket'
        if (!is_null($bucket) && $bucket instanceof Bucket) {
            $path .= '/' . urlencode($bucket->name);
        }

        # Add '.../key'
        if (!is_null($key)) {
            $path .= '/' . urlencode($key);
        }

        # Add '.../bucket,tag,acc/bucket,tag,acc'
        if (!is_null($spec)) {
            $s = '';
            foreach ($spec as $el) {
                if ($s != '') {
                    $s .= '/';
                }
                $s .= urlencode($el[0]) . ',' . urlencode($el[1]) . ',' . $el[2] . '/';
            }
            $path .= '/' . $s;
        }

        # Add query parameters.
        if (!is_null($params)) {
            $s = '';
            foreach ($params as $key => $value) {
                if ($s != '') {
                    $s .= '&';
                }
                $s .= urlencode($key) . '=' . urlencode($value);
            }

            $path .= '?' . $s;
        }

        return $path;
    }

    /**
     * Construct secondary index URLs
     *
     * Given a Client, Bucket, Key, LinkSpec, and Params,
     * construct and return a URL for searching secondary indexes.
     *
     * @author Eric Stevens <estevens@taglabsinc.com>
     *
     * @param \Basho\Riak\Riak $client
     * @param \Basho\Riak\Bucket $bucket
     * @param string $index - Index Name & type (eg, "indexName_bin")
     * @param string|int $start - Starting value or exact match if no ending value
     * @param string|int $end - Ending value for range search
     * @return string URL
     */
    public static function buildIndexPath(Riak $client, Bucket $bucket, $index, $start, $end = null)
    {
        # Build 'http://hostname:port/prefix/bucket'
        $path = array('http:/', $client->host . ':' . $client->port, $client->indexPrefix);

        # Add '.../bucket'
        $path[] = urlencode($bucket->name);

        # Add '.../index'
        $path[] = 'index';

        # Add '.../index_type'
        $path[] = urlencode($index);

        # Add .../(start|exact)
        $path[] = urlencode($start);

        if (!is_null($end)) {
            $path[] = urlencode($end);
        }

        // faster than repeated string concatenations
        $path = join('/', $path);

        return $path;
    }

    /**
     * Send HTTP request
     *
     * Given a Method, URL, Headers, and Body, perform and HTTP request,
     * and return an array of arity 2 containing an associative array of
     * response headers and the response body.
     *
     * @return array
     */
    public static function httpRequest($method, $url, $request_headers = array(), $obj = '')
    {
        # Set up curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        } else {
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
            } else {
                if ($method == 'PUT') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
                } else {
                    if ($method == 'DELETE') {
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    } else {
                        if ($method == 'HEAD') {
                            curl_setopt($ch, CURLOPT_NOBODY, 1);
                        }
                    }
                }
            }
        }

        # Capture the response headers...
        $response_headers_io = new StringIO();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$response_headers_io, 'write'));

        # Capture the response body...
        $response_body_io = new StringIO();
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, array(&$response_body_io, 'write'));

        try {
            # Run the request.
            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            # Get the headers...
            $parsed_headers = Utils::parseHttpHeaders($response_headers_io->contents());
            $response_headers = array("http_code" => $http_code);
            foreach ($parsed_headers as $key => $value) {
                $response_headers[strtolower($key)] = $value;
            }

            # Get the body...
            $response_body = $response_body_io->contents();

            # Return a new RiakResponse object.
            return array($response_headers, $response_body);
        } catch (Exception $e) {
            curl_close($ch);
            error_log('Error: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Parse HTTP headers
     *
     * Parse an HTTP Header string into an asssociative array of
     * response headers.
     *
     * @param mixed $headers
     *
     * @return array
     */
    static function parseHttpHeaders($headers)
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace_callback(
                    '/(?<=^|[\x09\x20\x2D])./',
                    function ($matches) {
                        return strtoupper($matches[0]);
                    }, strtolower(trim($match[1]))
                );
                if (isset($retVal[$match[1]])) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }

        return $retVal;
    }
}

<?php
/**
 * This file is part of the riak-php-client.
 *
 * PHP version 5.3+
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link https://github.com/localgod/riak-php-client
 */
namespace Basho\Riak;

/**
 * Utility functions used by Riak library.
 */
class Utils
{
    /**
     * Get value
     *
     * @param string $key          Key
     * @param array  $array        List
     * @param mixed  $defaultValue Default value
     *
     * @return mixed
     */
    public static function getValue($key, $array, $defaultValue)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $defaultValue;
        }
    }

    /**
     * This method is only here to maintain backwards compatibility
     * with old method names pre PSR coding standard
     *
     * @param string $name      Name of old method
     * @param array  $arguments Arguments for method
     *
     * @return void
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name == 'get_value') {
            self::getValue($arguments[0], $arguments[1], $arguments[2]);
        }
    }

    /**
     * Given a Client, Bucket, Key, LinkSpec, and Params,
     * construct and return a URL.
     *
     * @param Client      $client Riak client
     * @param Bucket|null $bucket Riak bucket
     * @param string|null $key    Key
     * @param array|null  $spec   Link spec
     * @param array|null  $params Parameters
     *
     * @return string
     */
    public static function buildRestPath(Client $client, $bucket = null, $key = null,
            $spec = null, $params = null)
    {
        # Build 'http://hostname:port/prefix/bucket'
        $path = 'http://';
        $path .= $client->host . ':' . $client->port;
        $path .= '/' . $client->prefix;

        # Add '.../bucket'
        if (!is_null($bucket) && $bucket instanceof Bucket) {
            $path .= '/' . urlencode($bucket->getName());
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
                $s .= urlencode($el[0]) . ',' . urlencode($el[1]) . ','
                        . $el[2] . '/';
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
     * Given a Client, Bucket, Key, LinkSpec, and Params,
     * construct and return a URL for searching secondary indexes.
     *
     * @author Eric Stevens <estevens@taglabsinc.com>
     *
     * @param Client              $client Riak client
     * @param Bucket              $bucket Riak bucket
     * @param string              $index  Index Name & type
     *                                    (eg, "indexName_bin")
     * @param string|integer      $start  Starting value or exact
     *                                    match if no ending value
     * @param string|integer|null $end Ending value for range search
     *
     * @return string URL
     */
    public static function buildIndexPath(Client $client,
            Bucket $bucket, $index, $start, $end = null)
    {
        # Build 'http://hostname:port/prefix/bucket'
        $path = array('http:/', $client->host . ':' . $client->port,
                $client->indexPrefix);

        # Add '.../bucket'
        $path[] = urlencode($bucket->getName());

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
     * Given a Method, URL, Headers, and Body, perform and HTTP request,
     * and return an array of arity 2 containing an associative array of
     * response headers and the response body.
     *
     * @param string $method         Http method
     * @param string $url            Url
     * @param array  $requestHeaders Request headers
     * @param string $obj            Object
     *
     * @return array
     */
    public static function httpRequest($method, $url,
            $requestHeaders = array(), $obj = '')
    {
        # Set up curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);

        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        } elseif ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $obj);
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        # Capture the response headers...
        $responseHeadersIO = new StringIO();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
                array(&$responseHeadersIO, 'write'));

        # Capture the response body...
        $responseBodyIO = new StringIO();
        curl_setopt($ch, CURLOPT_WRITEFUNCTION,
                array(&$responseBodyIO, 'write'));

        try {
            # Run the request.
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            # Get the headers...
            $parsedHeaders = Utils::parseHttpHeaders(
                    $responseHeadersIO->contents());
            $responseHeaders = array("http_code" => $httpCode);
            foreach ($parsedHeaders as $key => $value) {
                $responseHeaders[strtolower($key)] = $value;
            }

            # Get the body...
            $responseBody = $responseBodyIO->contents();

            # Return a new RiakResponse object.

            return array($responseHeaders, $responseBody);
        } catch (\Exception $e) {
            curl_close($ch);
            error_log('Error: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Parse an HTTP Header string into an associative array of
     * response headers.
     *
     * @param string $headers Headers to parse
     *
     * @return array
     */
    public static function parseHttpHeaders($headers)
    {
        $retVal = array();
        $fields = explode("\r\n",
                preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e',
                        'strtoupper("\0")', strtolower(trim($match[1])));
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

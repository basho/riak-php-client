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

use Basho\Riak\Exception,
    Basho\Riak\Link,
    Basho\Riak\Link\Phase as LinkPhase,
    Basho\Riak\MapReduce\Phase as MapReducePhase,
    Basho\Riak\Object,
    Basho\Riak\Utils;

/**
 * MapReduce
 *
 * @category   Basho
 * @author     Riak team (https://github.com/basho/riak-php-client/contributors)
 */
class MapReduce
{

    /**
     * Construct a Map/Reduce object.
     *
     * @param \Basho\Riak\Riak $client - A Client object.
     * @return MapReduce
     */
    public function __construct($client)
    {
        $this->client = $client;
        $this->phases = array();
        $this->inputs = array();
        $this->input_mode = null;
        $this->key_filters = array();
        $this->index = array();
    }

    /**
     * Add inputs to a map/reduce operation
     *
     * This method takes three different forms,
     * depending on the provided inputs. You can
     * specify either  a Object, a string bucket name,
     * or a bucket, key, and additional arg.
     *
     * @param mixed $arg1 Object or Bucket
     * @param mixed $arg2 Key or blank
     * @param mixed $arg3 Arg or blank
     * @return MapReduce
     */
    public function add($arg1, $arg2 = null, $arg3 = null)
    {
        if (func_num_args() == 1) {
            if ($arg1 instanceof Object) {
                return $this->add_object($arg1);
            } else {
                return $this->add_bucket($arg1);
            }
        }

        return $this->add_bucket_key_data($arg1, (string)$arg2, $arg3);
    }

    /**
     * Private
     *
     * @ignore
     */
    private function add_object($obj)
    {
        return $this->add_bucket_key_data($obj->bucket->name, $obj->key, null);
    }

    /**
     * Private
     *
     * @ignore
     */
    private function add_bucket_key_data($bucket, $key, $data)
    {
        if ($this->input_mode == "bucket") {
            throw new Exception("Already added a bucket, can't add an object.");
        }
        $this->inputs[] = array($bucket, $key, $data);

        return $this;
    }

    /**
     * Private
     *
     * @ignore
     * @return $this
     */
    private function add_bucket($bucket)
    {
        $this->input_mode = "bucket";
        $this->inputs = $bucket;

        return $this;
    }

    /**
     * Begin a map/reduce operation using a Search
     *
     * This command will
     * return an error unless executed against a Riak Search cluster.
     *
     * @param string $bucket - The Bucket to search.  @param string
     * query - The Query to execute. (Lucene syntax.)  @return \Basho\Riak\MapReduce
     */
    public function search($bucket, $query)
    {
        $this->inputs = array(
            "module" => "riak_search",
            "function" => "mapred_search",
            "arg" => array($bucket, $query)
        );

        return $this;
    }

    /**
     * Add a link phase to the map/reduce operation
     *
     * @param string $bucket - Bucket name (default '_', which means all
     * buckets)
     * @param string $tag - Tag (default '_', which means all buckets)
     * @param boolean $keep - Flag whether to keep results from this
     * stage in the map/reduce. (default FALSE, unless this is the last
     * step in the phase)
     * @return $this
     */
    public function link($bucket = '_', $tag = '_', $keep = false)
    {
        $this->phases[] = new LinkPhase($bucket, $tag, $keep);

        return $this;
    }

    /**
     * Add a map phase to the map/reduce operation
     *
     * @param mixed $function - Either a named Javascript function (ie:
     * "Riak.mapValues"), or an anonymous javascript function (ie:
     * "function(...) { ... }" or an array ["erlang_module",
     * "function"].
     * @param array() $options - An optional associative array
     * containing "language", "keep" flag, and/or "arg".
     * @return $this
     */
    public function map($function, $options = array())
    {
        $language = is_array($function) ? "erlang" : "javascript";
        $this->phases[] = new MapReducePhase("map",
            $function,
            Utils::get_value("language", $options, $language),
            Utils::get_value("keep", $options, false),
            Utils::get_value("arg", $options, null));

        return $this;
    }

    /**
     * Add a reduce phase to the map/reduce operation
     *
     * @param mixed $function - Either a named Javascript function (ie:
     * "Riak.mapValues"), or an anonymous javascript function (ie:
     * "function(...) { ... }" or an array ["erlang_module",
     * "function"].
     * @param array() $options - An optional associative array
     * containing "language", "keep" flag, and/or "arg".
     * @return $this
     */
    public function reduce($function, $options = array())
    {
        $language = is_array($function) ? "erlang" : "javascript";
        $this->phases[] = new MapReducePhase("reduce",
            $function,
            Utils::get_value("language", $options, $language),
            Utils::get_value("keep", $options, false),
            Utils::get_value("arg", $options, null));

        return $this;
    }

    /**
     * Add a key filter to the map/reduce operation
     *
     * If there are already
     * existing filters, an "and" condition will be used to combine them.
     * Alias for key_filter_and
     *
     * @param array $filter - a key filter (ie:
     * ->key_filter(
     *     array("tokenize", "-", 2),
     *     array("between", "20110601", "20110630")
     * )
     * @return $this
     */
    public function key_filter(array $filter /*. ,$filter .*/)
    {
        $args = func_get_args();
        array_unshift($args, 'and');

        return call_user_func_array(array($this, 'key_filter_operator'), $args);
    }

    /**
     * Add a key filter to the map/reduce operation
     *
     * If there are already
     * existing filters, an "and" condition will be used to combine them.
     *
     * @param array $filter - a key filter (ie:
     * ->key_filter(
     *     array("tokenize", "-", 2),
     *     array("between", "20110601", "20110630")
     * )
     * @return $this
     */
    public function key_filter_and(array $filter)
    {
        $args = func_get_args();
        array_unshift($args, 'and');

        return call_user_func_array(array($this, 'key_filter_operator'), $args);
    }

    /**
     * Adds a key filter to the map/reduce operation
     *
     * If there are already
     * existing filters, an "or" condition will be used to combine with the
     * existing filters.
     *
     * @param array $filter
     * @return $this
     */
    public function key_filter_or(array $filter /*. ,$filter .*/)
    {
        $args = func_get_args();
        array_unshift($args, 'or');
        return call_user_func_array(array($this, 'key_filter_operator'), $args);
    }

    /**
     * Adds a key filter to the map/reduce operation
     *
     * If there are already
     * existing filters, the provided conditional operator will be used
     * to combine with the existing filters.
     *
     * @param string $operator - Operator (usually "and" or "or")
     * @param array $filter
     * @return $this
     */
    public function key_filter_operator($operator, $filter /*. ,$filter .*/)
    {
        $filters = func_get_args();
        array_shift($filters);
        if ($this->input_mode != 'bucket') {
            throw new Exception("Key filters can only be used in bucket mode");
        }

        if (count($this->index)) {
            throw new Exception("You cannot use index search and key filters on the same operation");
        }

        if (count($this->key_filters) > 0) {
            $this->key_filters = array(
                array(
                    $operator,
                    $this->key_filters,
                    $filters
                )
            );
        } else {
            $this->key_filters = $filters;
        }

        return $this;
    }

    /**
     * Performs an index search as part of a Map/Reduce operation
     *
     * Note that you can only do index searches on a bucket, so
     * this is incompatible with object or key operations, as well
     * as key filter operations.
     *
     * @param string $indexName The name of the index to search.
     * @param string $indexType The index type ('bin' or 'int')
     * @param string|int $startOrExact Start value to search for, or
     * exact value if no end value specified.
     * @param string|int optional $end End value to search for during
     * a range search
     * @return $this
     */
    public function indexSearch($indexName, $indexType, $startOrExact, $end = null)
    {
        // Check prerequisites
        if (count($this->key_filters)) {
            throw new Exception("You cannot use index search and key filters on the same operation");
        }

        if ($this->input_mode != 'bucket') {
            throw new Exception("Key filters can only be used in bucket mode");
        }

        if ($end === null) {
            $this->index = array(
                'index' => "{$indexName}_{$indexType}",
                'key' => urlencode($startOrExact)
            );
        } else {
            $this->index = array(
                'index' => "{$indexName}_{$indexType}",
                'start' => urlencode($startOrExact),
                'end' => urlencode($end)
            );
        }

        return $this;

    }


    /**
     * Run the map/reduce operation
     *
     * Returns an array of results, or an
     * array of Link objects if the last phase is a link phase.
     *
     * @param integer $timeout - Timeout in seconds.
     * @return array()
     */
    public function run($timeout = null)
    {
        $num_phases = count($this->phases);

        $linkResultsFlag = false;

        # If there are no phases, then just echo the inputs back to the user.
        if ($num_phases == 0) {
            $this->reduce(array("riak_kv_mapreduce", "reduce_identity"));
            $num_phases = 1;
            $linkResultsFlag = true;
        }

        # Convert all phases to associative arrays. Also,
        # if none of the phases are accumulating, then set the last one to
        # accumulate.
        $keep_flag = false;
        $query = array();
        for ($i = 0; $i < $num_phases; $i++) {
            $phase = $this->phases[$i];
            if ($i == ($num_phases - 1) && !$keep_flag) {
                $phase->keep = true;
            }
            if ($phase->keep) {
                $keep_flag = true;
            }
            $query[] = $phase->to_array();
        }

        # Add key filters if applicable
        if ($this->input_mode == 'bucket' && count($this->key_filters) > 0) {
            $this->inputs = array(
                'bucket' => $this->inputs,
                'key_filters' => $this->key_filters
            );
        }

        # Add index search if applicable
        if ($this->input_mode == 'bucket' && count($this->index) > 0) {
            $this->inputs = array_merge(array('bucket' => $this->inputs), $this->index);
        }

        # Construct the job, optionally set the timeout...
        $job = array("inputs" => $this->inputs, "query" => $query);
        if ($timeout != null) {
            $job["timeout"] = $timeout;
        }
        $content = json_encode($job);

        # Do the request...
        $url = "http://" . $this->client->host . ":" . $this->client->port . "/" . $this->client->mapred_prefix;
        $response = Utils::httpRequest('POST', $url, array('Content-type: application/json'), $content);
        $result = json_decode($response[1]);

        # If the last phase is NOT a link phase, then return the result.
        $linkResultsFlag |= (end($this->phases) instanceof LinkPhase);

        # If we don't need to link results, then just return.
        if (!$linkResultsFlag) {
            return $result;
        }

        # Otherwise, if the last phase IS a link phase, then convert the
        # results to Link objects.
        $a = array();
        foreach ($result as $r) {
            $tag = isset($r[2]) ? $r[2] : null;
            $link = new Link($r[0], $r[1], $tag);
            $link->client = $this->client;
            $a[] = $link;
        }

        return $a;
    }
}

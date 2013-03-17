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
 * The MapReduce object allows you to build up and run a
 * map/reduce operation on Riak.
 *
 * @method MapReduce key_filter(array $filter)
 * @method MapReduce key_filter_and(array $filter)
 * @method MapReduce key_filter_or(array $filter)
 * @method MapReduce key_filter_operator($operator, array $filter)
 */
class MapReduce
{
    /**
     * Riak client
     * @var Client
     */
    private $client;

    /**
     * A list of phases
     * @var Phase[]
     */
    private $phases;

    /**
     * A list of inputs
     * @var array
     */
    private $inputs;

    /**
     * Input mode
     * @var string|null
     */
    private $inputMode;

    /**
     * List of key filters
     * @var array
     */
    private $keyFilters;

    /**
     * Indexes
     * @var array
     */
    private $index;

    /**
     * Construct a Map/Reduce object.
     *
     * @param Client $client A Client object.
     *
     * @return MapReduce
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->phases = array();
        $this->inputs = array();
        $this->inputMode = null;
        $this->keyFilters = array();
        $this->index = array();
    }

    /**
     * Add inputs to a map/reduce operation.
     *
     * This method takes three different forms, depending on the provided
     * inputs. You can specify either  a Object, a string bucket name, or
     * a bucket, key, and additional arg.
     *
     * @param mixed  $arg1 Object or Bucket
     * @param string $arg2 Key or blank
     * @param mixed  $arg3 Arg or blank
     *
     * @return MapReduce
     */
    public function add($arg1, $arg2 = null, $arg3 = null)
    {
        if (func_num_args() == 1) {
            if ($arg1 instanceof Object) {
                return $this->addObject($arg1);
            } else {
                return $this->addBucket($arg1);
            }
        }

        return $this->addBucketKeyData($arg1, (string) $arg2, $arg3);
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
    public function __call($name, $arguments)
    {
        if ($name == 'key_filter') {
            return call_user_func_array(array($this, "keyFilter"), $arguments);
        }
        if ($name == 'key_filter_and') {
            return call_user_func_array(array($this, "keyFilterAnd"), $arguments);
        }
        if ($name == 'key_filter_or') {
            return call_user_func_array(array($this, "keyFilterOr"), $arguments);
        }
        if ($name == 'key_filter_operator') {
            return call_user_func_array(array($this, "keyFilterOperator"), $arguments);
        }
        return null;
    }

    /**
     * Private.
     *
     * @param mixed $obj
     *
     * @return MapReduce
     */
    private function addObject($obj)
    {
        return $this->addBucketKeyData($obj->bucket->name, $obj->key, null);
    }

    /**
     * Add bucket key data
     *
     * @param mixed $bucket
     * @param mixed $key
     * @param mixed $data
     *
     * @throws \Exception if trying to add object when already added a bucket
     * @return MapReduce
     */
    private function addBucketKeyData($bucket, $key, $data)
    {
        if ($this->inputMode == "bucket") {
            throw new \Exception("Already added a bucket, can't add an object.");
        }
        $this->inputs[] = array($bucket, $key, $data);

        return $this;
    }

    /**
     * Add bucket
     *
     * @param mixed $bucket
     *
     * @return MapReduce
     */
    private function addBucket($bucket)
    {
        $this->inputMode = "bucket";
        $this->inputs = $bucket;

        return $this;
    }

    /**
     * Begin a map/reduce operation using a Search.
     *
     * This command will return an error unless executed against a Riak
     * Search cluster.
     *
     * @param string $bucket The Bucket to search.
     * @param string $query  The Query to execute. (Lucene syntax.)
     *
     * @return MapReduce
     */
    public function search($bucket, $query)
    {
        $this->inputs = array("module" => "riak_search",
                "function" => "mapred_search", "arg" => array($bucket, $query));

        return $this;
    }

    /**
     * Add a link phase to the map/reduce operation.
     *
     * @param string  $bucket Bucket name (default '_', which means all buckets)
     * @param string  $tag    Tag (default '_', which means all buckets)
     * @param boolean $keep   Flag whether to keep results from this stage in
     *                        the map/reduce. (default false, unless this is the
     *                        last step in the phase)
     * @return MapReduce
     */
    public function link($bucket = '_', $tag = '_', $keep = false)
    {
        $this->phases[] = new Link\Phase($bucket, $tag, $keep);

        return $this;
    }

    /**
     * Add a map phase to the map/reduce operation.
     *
     * @param mixed $function Either a named Javascript function
     *                        (ie: "Riak.mapValues"), or an anonymous
     *                        javascript function (ie: "function(...) { ... }"
     *                        or an array ["erlang_module", * "function"].
     * @param array $options  An optional associative array containing
     *                        "language", "keep" flag, and/or "arg".
     *
     * @return MapReduce
     */
    public function map($function, $options = array())
    {
        $language = is_array($function) ? "erlang" : "javascript";
        $this->phases[] = new MapReduce\Phase("map", $function,
                Utils::getValue("language", $options, $language),
                Utils::getValue("keep", $options, false),
                Utils::getValue("arg", $options, null));

        return $this;
    }

    /**
     * Add a reduce phase to the map/reduce operation.
     *
     * @param mixed $function Either a named Javascript function
     *                        (ie: "Riak.mapValues"), or an anonymous
     *                        javascript function (ie: "function(...) { ... }"
     *                        or an array ["erlang_module", "function"].
     * @param array $options  An optional associative array containing
     *                        "language", "keep" flag, and/or "arg".
     *
     * @return MapReduce
     */
    public function reduce($function, $options = array())
    {
        $language = is_array($function) ? "erlang" : "javascript";
        $this->phases[] = new MapReduce\Phase("reduce", $function,
                Utils::getValue("language", $options, $language),
                Utils::getValue("keep", $options, false),
                Utils::getValue("arg", $options, null));

        return $this;
    }

    /**
     * Add a key filter to the map/reduce operation.
     *
     * If there are already existing filters, an "and" condition will be used
     * to combine them. Alias for key_filter_and
     *
     * @param array $filter A key filter (ie:->keyFilter(array("tokenize", "-", 2),
     *                      array("between", "20110601", "20110630"))
     *
     * @return MapReduce
     */
    public function keyFilter(array $filter /*. ,$filter .*/)
    {
        $args = func_get_args();
        array_unshift($args, 'and');

        return call_user_func_array(array($this, 'key_filter_operator'), $args);
    }

    /**
     * Add a key filter to the map/reduce operation.
     *
     * If there are already existing filters, an "and" condition will be used
     * to combine them.
     *
     * @param array $filter A key filter (ie:->keyFilter(array("tokenize", "-", 2),
     *                      array("between", "20110601", "20110630"))
     *
     * @return MapReduce
     */
    public function keyFilterAnd(array $filter)
    {
        $args = func_get_args();
        array_unshift($args, 'and');

        return call_user_func_array(array($this, 'key_filter_operator'), $args);
    }

    /**
     * Adds a key filter to the map/reduce operation.
     *
     * If there are already existing filters, an "or" condition will be used to
     * combine with the existing filters.
     *
     * @param array $filter Filter
     *
     * @return MapReduce
     */
    public function keyFilterOr(array $filter /*. ,$filter .*/)
    {
        $args = func_get_args();
        array_unshift($args, 'or');

        return call_user_func_array(array($this, 'key_filter_operator'), $args);
    }

    /**
     * Adds a key filter to the map/reduce operation.
     *
     * If there are already existing filters, the provided conditional operator
     * will be used to combine with the existing filters.
     *
     * @param string $operator Operator (usually "and" or "or")
     * @param array  $filter   Filter
     *
     * @throws \Exception if key filters can't be used
     * @return MapReduce
     */
    public function keyFilterOperator($operator, $filter /*. ,$filter .*/)
    {
        $filters = func_get_args();
        array_shift($filters);
        if ($this->inputMode != 'bucket') {
            throw new \Exception("Key filters can only be used in bucket mode");
        }
        if (count($this->index)) {
            throw new \Exception(
            "You cannot use index search and key filters on the same operation"
            );
        }

        if (count($this->keyFilters) > 0) {
            $this->keyFilters = array(
                    array($operator, $this->keyFilters, $filters));
        } else {
            $this->keyFilters = $filters;
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
     * @param string         $indexName    The name of the index to search.
     * @param string         $indexType    The index type ('bin' or 'int')
     * @param string|integer $startOrExact Start value to search for, or exact
     *                                     value if no end value specified.
     * @param string|integer $end          Optional. End value to search for
     *                                     during a range search
     *
     * @throws \Exception if index search can't be used
     * @return MapReduce
     */
    public function indexSearch($indexName, $indexType, $startOrExact,
            $end = null)
    {
        // Check prerequisites
        if (count($this->keyFilters)) {
            throw new \Exception(
            "You cannot use index search and key filters on the same operation"
            );
        }
        if ($this->inputMode != 'bucket') {
            throw new \Exception("Key filters can only be used in bucket mode");
        }

        if ($end === null) {
            $this->index = array('index' => "{$indexName}_{$indexType}",
            'key' => urlencode($startOrExact));
        } else {
            $this->index = array('index' => "{$indexName}_{$indexType}",
            'start' => urlencode($startOrExact),
            'end' => urlencode($end));
        }

        return $this;
    }

    /**
     * Run the map/reduce operation.
     *
     * Returns an array of results, or an array of Link objects if the last
     * phase is a link phase.
     *
     * @param integer $timeout Timeout in seconds.
     *
     * @return array()
     */
    public function run($timeout = null)
    {
        $numPhases = count($this->phases);

        $linkResultsFlag = false;

        # If there are no phases, then just echo the inputs back to the user.
        if ($numPhases == 0) {
            $this->reduce(array("riak_kv_mapreduce", "reduce_identity"));
            $numPhases = 1;
            $linkResultsFlag = true;
        }

        # Convert all phases to associative arrays. Also,
        # if none of the phases are accumulating, then set the last one to
        # accumulate.
        $keepFlag = false;
        $query = array();
        for ($i = 0; $i < $numPhases; $i++) {
            $phase = $this->phases[$i];
            if ($i == ($numPhases - 1) && !$keepFlag) {
                $phase->setKeep(true);
            }
            if ($phase->getKeep()) {
                $keepFlag = true;
            }
            $query[] = $phase->toArray();
        }

        # Add key filters if applicable
        if ($this->inputMode == 'bucket' && count($this->keyFilters) > 0) {
            $this->inputs = array('bucket' => $this->inputs,
                    'key_filters' => $this->keyFilters);
        }

        # Add index search if applicable
        if ($this->inputMode == 'bucket' && count($this->index) > 0) {
            $this->inputs = array_merge(array('bucket' => $this->inputs),
                    $this->index);
        }

        # Construct the job, optionally set the timeout...
        $job = array("inputs" => $this->inputs, "query" => $query);
        if ($timeout != null) {
            $job["timeout"] = $timeout;
        }
        $content = json_encode($job);

        # Do the request...
        $url = "http://" . $this->client->host . ":" . $this->client->port
        . "/" . $this->client->mapredPrefix;
        $response = Utils::httpRequest('POST', $url,
                array('Content-type: application/json'), $content);
        $result = json_decode($response[1]);

        # If the last phase is NOT a link phase, then return the result.
        $linkResultsFlag |= (end($this->phases) instanceof Link\Phase);

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
            $link->setClient($this->client);
            $a[] = $link;
        }

        return $a;
    }
}

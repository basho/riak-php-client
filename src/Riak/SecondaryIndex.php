<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 3/25/15
 * Time: 4:19 PM
 */

namespace Riak;


abstract class SecondaryIndex {
    public static $IndexSuffixLen = 4;
    public static $IndexHeaderPrefix = "x-riak-index-";
    public static $IndexHeaderPrefixLen = 13;

    protected $name = "";
    protected $values = array();

    public function __construct($values = NULL) {
        if (!empty($values) && is_array($values)) {
            $this->values = $values;
        }

        elseif (!empty($values)) {
            array_push($this->values, $values);
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getValues() {
        return $this->values;
    }

    public function addValue($value) {
        array_push($this->values, $value);
    }

    public function getHeaderName() {
        return $this->IndexHeaderPrefix . $this->name . $this->getIndexSuffix();
    }

    protected abstract function getIndexSuffix();

}

class StringSecondaryIndex extends SecondaryIndex {
    private $indexSuffix = "_bin";

    protected function getIndexSuffix()
    {
        return $this->$indexSuffix;
    }
}

class IntSecondaryIndex extends SecondaryIndex {
    private $indexSuffix = "_int";

    protected function getIndexSuffix()
    {
        return $this->$indexSuffix;
    }
}
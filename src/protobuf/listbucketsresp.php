<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;


// message ListBucketsResp
class ListBucketsResp {
  private $_unknown;
  
  function __construct($in = NULL, &$limit = PHP_INT_MAX) {
    if($in !== NULL) {
      if (is_string($in)) {
        $fp = fopen('php://memory', 'r+b');
        fwrite($fp, $in);
        rewind($fp);
      } else if (is_resource($in)) {
        $fp = $in;
      } else {
        throw new Exception('Invalid in parameter');
      }
      $this->read($fp, $limit);
    }
  }
  
  function read($fp, &$limit = PHP_INT_MAX) {
    while(!feof($fp) && $limit > 0) {
      $tag = Protobuf::read_varint($fp, $limit);
      if ($tag === false) break;
      $wire  = $tag & 0x07;
      $field = $tag >> 3;
      //var_dump("ListBucketsResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->buckets_[] = $tmp;
          $limit-=$len;
          break;
        default:
          $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
      }
    }
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
  }
  
  function write($fp) {
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
    if (!is_null($this->buckets_))
      foreach($this->buckets_ as $v) {
        fwrite($fp, "\x0a");
        Protobuf::write_varint($fp, strlen($v));
        fwrite($fp, $v);
      }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->buckets_))
      foreach($this->buckets_ as $v) {
        $l = strlen($v);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    return $size;
  }
  
  public function validateRequired() {
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('buckets_', $this->buckets_);
  }
  
  // repeated bytes buckets = 1;

  private $buckets_ = null;
  public function clearBuckets() { $this->buckets_ = null; }
  public function getBucketsCount() { if ($this->buckets_ === null ) return 0; else return count($this->buckets_); }
  public function getBuckets($index) { return $this->buckets_[$index]; }
  public function getBucketsArray() { if ($this->buckets_ === null ) return array(); else return $this->buckets_; }
  public function setBuckets($index, $value) {$this->buckets_[$index] = $value;	}
  public function addBuckets($value) { $this->buckets_[] = $value; }
  public function addAllBuckets(array $values) { foreach($values as $value) {$this->buckets_[] = $value;} }
  
  // @@protoc_insertion_point(class_scope:ListBucketsResp)
}
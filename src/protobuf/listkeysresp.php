<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message ListKeysResp
class ListKeysResp {
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
      //var_dump("ListKeysResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->keys_[] = $tmp;
          $limit-=$len;
          break;
        case 2:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->done_ = $tmp > 0 ? true : false;
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
    if (!is_null($this->keys_))
      foreach($this->keys_ as $v) {
        fwrite($fp, "\x0a");
        Protobuf::write_varint($fp, strlen($v));
        fwrite($fp, $v);
      }
    if (!is_null($this->done_)) {
      fwrite($fp, "\x10");
      Protobuf::write_varint($fp, $this->done_ ? 1 : 0);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->keys_))
      foreach($this->keys_ as $v) {
        $l = strlen($v);
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    if (!is_null($this->done_)) {
      $size += 2;
    }
    return $size;
  }
  
  public function validateRequired() {
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('keys_', $this->keys_)
         . Protobuf::toString('done_', $this->done_);
  }
  
  // repeated bytes keys = 1;

  private $keys_ = null;
  public function clearKeys() { $this->keys_ = null; }
  public function getKeysCount() { if ($this->keys_ === null ) return 0; else return count($this->keys_); }
  public function getKeys($index) { return $this->keys_[$index]; }
  public function getKeysArray() { if ($this->keys_ === null ) return array(); else return $this->keys_; }
  public function setKeys($index, $value) {$this->keys_[$index] = $value;	}
  public function addKeys($value) { $this->keys_[] = $value; }
  public function addAllKeys(array $values) { foreach($values as $value) {$this->keys_[] = $value;} }
  
  // optional bool done = 2;

  private $done_ = null;
  public function clearDone() { $this->done_ = null; }
  public function hasDone() { return $this->done_ !== null; }
  public function getDone() { if($this->done_ === null) return false; else return $this->done_; }
  public function setDone($value) { $this->done_ = $value; }
  
  // @@protoc_insertion_point(class_scope:ListKeysResp)
}
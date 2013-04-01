<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message BucketProps
class BucketProps {
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
      //var_dump("BucketProps: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->nVal_ = $tmp;
          
          break;
        case 2:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->allowMult_ = $tmp > 0 ? true : false;
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
    if (!is_null($this->nVal_)) {
      fwrite($fp, "\x08");
      Protobuf::write_varint($fp, $this->nVal_);
    }
    if (!is_null($this->allowMult_)) {
      fwrite($fp, "\x10");
      Protobuf::write_varint($fp, $this->allowMult_ ? 1 : 0);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->nVal_)) {
      $size += 1 + Protobuf::size_varint($this->nVal_);
    }
    if (!is_null($this->allowMult_)) {
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
         . Protobuf::toString('nVal_', $this->nVal_)
         . Protobuf::toString('allowMult_', $this->allowMult_);
  }
  
  // optional uint32 n_val = 1;

  private $nVal_ = null;
  public function clearNVal() { $this->nVal_ = null; }
  public function hasNVal() { return $this->nVal_ !== null; }
  public function getNVal() { if($this->nVal_ === null) return 0; else return $this->nVal_; }
  public function setNVal($value) { $this->nVal_ = $value; }
  
  // optional bool allow_mult = 2;

  private $allowMult_ = null;
  public function clearAllowMult() { $this->allowMult_ = null; }
  public function hasAllowMult() { return $this->allowMult_ !== null; }
  public function getAllowMult() { if($this->allowMult_ === null) return false; else return $this->allowMult_; }
  public function setAllowMult($value) { $this->allowMult_ = $value; }
  
  // @@protoc_insertion_point(class_scope:BucketProps)
}

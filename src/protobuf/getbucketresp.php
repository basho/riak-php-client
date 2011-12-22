<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;


// message GetBucketResp
class GetBucketResp {
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
      //var_dump("GetBucketResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->props_ = new BucketProps($fp, $len);
          ASSERT('$len == 0');
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
    if (!is_null($this->props_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, $this->props_->size()); // message
      $this->props_->write($fp);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->props_)) {
      $l = $this->props_->size();
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->props_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('props_', $this->props_);
  }
  
  // required .BucketProps props = 1;

  private $props_ = null;
  public function clearProps() { $this->props_ = null; }
  public function hasProps() { return $this->props_ !== null; }
  public function getProps() { if($this->props_ === null) return null; else return $this->props_; }
  public function setProps(BucketProps $value) { $this->props_ = $value; }
  
  // @@protoc_insertion_point(class_scope:GetBucketResp)
}
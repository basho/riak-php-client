<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message SetBucketReq
class SetBucketReq {
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
      //var_dump("SetBucketReq: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->bucket_ = $tmp;
          $limit-=$len;
          break;
        case 2:
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
    if (!is_null($this->bucket_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->bucket_));
      fwrite($fp, $this->bucket_);
    }
    if (!is_null($this->props_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, $this->props_->size()); // message
      $this->props_->write($fp);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->bucket_)) {
      $l = strlen($this->bucket_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->props_)) {
      $l = $this->props_->size();
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->bucket_ === null) return false;
    if ($this->props_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('bucket_', $this->bucket_)
         . Protobuf::toString('props_', $this->props_);
  }
  
  // required bytes bucket = 1;

  private $bucket_ = null;
  public function clearBucket() { $this->bucket_ = null; }
  public function hasBucket() { return $this->bucket_ !== null; }
  public function getBucket() { if($this->bucket_ === null) return ""; else return $this->bucket_; }
  public function setBucket($value) { $this->bucket_ = $value; }
  
  // required .BucketProps props = 2;

  private $props_ = null;
  public function clearProps() { $this->props_ = null; }
  public function hasProps() { return $this->props_ !== null; }
  public function getProps() { if($this->props_ === null) return null; else return $this->props_; }
  public function setProps(BucketProps $value) { $this->props_ = $value; }
  
  // @@protoc_insertion_point(class_scope:SetBucketReq)
}

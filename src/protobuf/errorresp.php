<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message ErrorResp
class ErrorResp {
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
      //var_dump("ErrorResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->errmsg_ = $tmp;
          $limit-=$len;
          break;
        case 2:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->errcode_ = $tmp;
          
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
    if (!is_null($this->errmsg_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->errmsg_));
      fwrite($fp, $this->errmsg_);
    }
    if (!is_null($this->errcode_)) {
      fwrite($fp, "\x10");
      Protobuf::write_varint($fp, $this->errcode_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->errmsg_)) {
      $l = strlen($this->errmsg_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->errcode_)) {
      $size += 1 + Protobuf::size_varint($this->errcode_);
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->errmsg_ === null) return false;
    if ($this->errcode_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('errmsg_', $this->errmsg_)
         . Protobuf::toString('errcode_', $this->errcode_);
  }
  
  // required bytes errmsg = 1;

  private $errmsg_ = null;
  public function clearErrmsg() { $this->errmsg_ = null; }
  public function hasErrmsg() { return $this->errmsg_ !== null; }
  public function getErrmsg() { if($this->errmsg_ === null) return ""; else return $this->errmsg_; }
  public function setErrmsg($value) { $this->errmsg_ = $value; }
  
  // required uint32 errcode = 2;

  private $errcode_ = null;
  public function clearErrcode() { $this->errcode_ = null; }
  public function hasErrcode() { return $this->errcode_ !== null; }
  public function getErrcode() { if($this->errcode_ === null) return 0; else return $this->errcode_; }
  public function setErrcode($value) { $this->errcode_ = $value; }
  
  // @@protoc_insertion_point(class_scope:ErrorResp)
}

<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message MapRedReq
class MapRedReq {
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
      //var_dump("MapRedReq: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->request_ = $tmp;
          $limit-=$len;
          break;
        case 2:
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
          $this->contentType_ = $tmp;
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
    if (!is_null($this->request_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->request_));
      fwrite($fp, $this->request_);
    }
    if (!is_null($this->contentType_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->contentType_));
      fwrite($fp, $this->contentType_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->request_)) {
      $l = strlen($this->request_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->contentType_)) {
      $l = strlen($this->contentType_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->request_ === null) return false;
    if ($this->contentType_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('request_', $this->request_)
         . Protobuf::toString('contentType_', $this->contentType_);
  }
  
  // required bytes request = 1;

  private $request_ = null;
  public function clearRequest() { $this->request_ = null; }
  public function hasRequest() { return $this->request_ !== null; }
  public function getRequest() { if($this->request_ === null) return ""; else return $this->request_; }
  public function setRequest($value) { $this->request_ = $value; }
  
  // required bytes content_type = 2;

  private $contentType_ = null;
  public function clearContentType() { $this->contentType_ = null; }
  public function hasContentType() { return $this->contentType_ !== null; }
  public function getContentType() { if($this->contentType_ === null) return ""; else return $this->contentType_; }
  public function setContentType($value) { $this->contentType_ = $value; }
  
  // @@protoc_insertion_point(class_scope:MapRedReq)
}
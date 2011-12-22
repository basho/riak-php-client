<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message MapRedResp
class MapRedResp {
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
      //var_dump("MapRedResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->phase_ = $tmp;
          
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
          $this->response_ = $tmp;
          $limit-=$len;
          break;
        case 3:
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
    if (!is_null($this->phase_)) {
      fwrite($fp, "\x08");
      Protobuf::write_varint($fp, $this->phase_);
    }
    if (!is_null($this->response_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->response_));
      fwrite($fp, $this->response_);
    }
    if (!is_null($this->done_)) {
      fwrite($fp, "\x18");
      Protobuf::write_varint($fp, $this->done_ ? 1 : 0);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->phase_)) {
      $size += 1 + Protobuf::size_varint($this->phase_);
    }
    if (!is_null($this->response_)) {
      $l = strlen($this->response_);
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
         . Protobuf::toString('phase_', $this->phase_)
         . Protobuf::toString('response_', $this->response_)
         . Protobuf::toString('done_', $this->done_);
  }
  
  // optional uint32 phase = 1;

  private $phase_ = null;
  public function clearPhase() { $this->phase_ = null; }
  public function hasPhase() { return $this->phase_ !== null; }
  public function getPhase() { if($this->phase_ === null) return 0; else return $this->phase_; }
  public function setPhase($value) { $this->phase_ = $value; }
  
  // optional bytes response = 2;

  private $response_ = null;
  public function clearResponse() { $this->response_ = null; }
  public function hasResponse() { return $this->response_ !== null; }
  public function getResponse() { if($this->response_ === null) return ""; else return $this->response_; }
  public function setResponse($value) { $this->response_ = $value; }
  
  // optional bool done = 3;

  private $done_ = null;
  public function clearDone() { $this->done_ = null; }
  public function hasDone() { return $this->done_ !== null; }
  public function getDone() { if($this->done_ === null) return false; else return $this->done_; }
  public function setDone($value) { $this->done_ = $value; }
  
  // @@protoc_insertion_point(class_scope:MapRedResp)
}
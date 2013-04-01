<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message GetClientIdResp
class GetClientIdResp {
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
      //var_dump("GetClientIdResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->clientId_ = $tmp;
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
    if (!is_null($this->clientId_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->clientId_));
      fwrite($fp, $this->clientId_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->clientId_)) {
      $l = strlen($this->clientId_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->clientId_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('clientId_', $this->clientId_);
  }
  
  // required bytes client_id = 1;

  private $clientId_ = null;
  public function clearClientId() { $this->clientId_ = null; }
  public function hasClientId() { return $this->clientId_ !== null; }
  public function getClientId() { if($this->clientId_ === null) return ""; else return $this->clientId_; }
  public function setClientId($value) { $this->clientId_ = $value; }
  
  // @@protoc_insertion_point(class_scope:GetClientIdResp)
}

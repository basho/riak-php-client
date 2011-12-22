<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;


// message GetServerInfoResp
class GetServerInfoResp {
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
      //var_dump("GetServerInfoResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->node_ = $tmp;
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
          $this->serverVersion_ = $tmp;
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
    if (!is_null($this->node_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->node_));
      fwrite($fp, $this->node_);
    }
    if (!is_null($this->serverVersion_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->serverVersion_));
      fwrite($fp, $this->serverVersion_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->node_)) {
      $l = strlen($this->node_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->serverVersion_)) {
      $l = strlen($this->serverVersion_);
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
         . Protobuf::toString('node_', $this->node_)
         . Protobuf::toString('serverVersion_', $this->serverVersion_);
  }
  
  // optional bytes node = 1;

  private $node_ = null;
  public function clearNode() { $this->node_ = null; }
  public function hasNode() { return $this->node_ !== null; }
  public function getNode() { if($this->node_ === null) return ""; else return $this->node_; }
  public function setNode($value) { $this->node_ = $value; }
  
  // optional bytes server_version = 2;

  private $serverVersion_ = null;
  public function clearServerVersion() { $this->serverVersion_ = null; }
  public function hasServerVersion() { return $this->serverVersion_ !== null; }
  public function getServerVersion() { if($this->serverVersion_ === null) return ""; else return $this->serverVersion_; }
  public function setServerVersion($value) { $this->serverVersion_ = $value; }
  
  // @@protoc_insertion_point(class_scope:GetServerInfoResp)
}
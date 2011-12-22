<?php
namespace Riak\ProtoBuf;
use Riak\ProtoBuf;

// message PutResp
class PutResp {
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
      //var_dump("PutResp: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->content_[] = new Content($fp, $len);
          ASSERT('$len == 0');
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
          $this->vclock_ = $tmp;
          $limit-=$len;
          break;
        case 3:
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
          $this->key_ = $tmp;
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
    if (!is_null($this->content_))
      foreach($this->content_ as $v) {
        fwrite($fp, "\x0a");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
    if (!is_null($this->vclock_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->vclock_));
      fwrite($fp, $this->vclock_);
    }
    if (!is_null($this->key_)) {
      fwrite($fp, "\x1a");
      Protobuf::write_varint($fp, strlen($this->key_));
      fwrite($fp, $this->key_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->content_))
      foreach($this->content_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    if (!is_null($this->vclock_)) {
      $l = strlen($this->vclock_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->key_)) {
      $l = strlen($this->key_);
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
         . Protobuf::toString('content_', $this->content_)
         . Protobuf::toString('vclock_', $this->vclock_)
         . Protobuf::toString('key_', $this->key_);
  }
  
  // repeated .Content content = 1;

  private $content_ = null;
  public function clearContent() { $this->content_ = null; }
  public function getContentCount() { if ($this->content_ === null ) return 0; else return count($this->content_); }
  public function getContent($index) { return $this->content_[$index]; }
  public function getContentArray() { if ($this->content_ === null ) return array(); else return $this->content_; }
  public function setContent($index, $value) {$this->content_[$index] = $value;	}
  public function addContent($value) { $this->content_[] = $value; }
  public function addAllContent(array $values) { foreach($values as $value) {$this->content_[] = $value;} }
  
  // optional bytes vclock = 2;

  private $vclock_ = null;
  public function clearVclock() { $this->vclock_ = null; }
  public function hasVclock() { return $this->vclock_ !== null; }
  public function getVclock() { if($this->vclock_ === null) return ""; else return $this->vclock_; }
  public function setVclock($value) { $this->vclock_ = $value; }
  
  // optional bytes key = 3;

  private $key_ = null;
  public function clearKey() { $this->key_ = null; }
  public function hasKey() { return $this->key_ !== null; }
  public function getKey() { if($this->key_ === null) return ""; else return $this->key_; }
  public function setKey($value) { $this->key_ = $value; }
  
  // @@protoc_insertion_point(class_scope:PutResp)
}
<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message DelReq
class DelReq {
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
      //var_dump("DelReq: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->key_ = $tmp;
          $limit-=$len;
          break;
        case 3:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->rw_ = $tmp;
          
          break;
        case 4:
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
        case 5:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->r_ = $tmp;
          
          break;
        case 6:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->w_ = $tmp;
          
          break;
        case 7:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->pr_ = $tmp;
          
          break;
        case 8:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->pw_ = $tmp;
          
          break;
        case 9:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->dw_ = $tmp;
          
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
    if (!is_null($this->key_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->key_));
      fwrite($fp, $this->key_);
    }
    if (!is_null($this->rw_)) {
      fwrite($fp, "\x18");
      Protobuf::write_varint($fp, $this->rw_);
    }
    if (!is_null($this->vclock_)) {
      fwrite($fp, "\"");
      Protobuf::write_varint($fp, strlen($this->vclock_));
      fwrite($fp, $this->vclock_);
    }
    if (!is_null($this->r_)) {
      fwrite($fp, "(");
      Protobuf::write_varint($fp, $this->r_);
    }
    if (!is_null($this->w_)) {
      fwrite($fp, "0");
      Protobuf::write_varint($fp, $this->w_);
    }
    if (!is_null($this->pr_)) {
      fwrite($fp, "8");
      Protobuf::write_varint($fp, $this->pr_);
    }
    if (!is_null($this->pw_)) {
      fwrite($fp, "@");
      Protobuf::write_varint($fp, $this->pw_);
    }
    if (!is_null($this->dw_)) {
      fwrite($fp, "H");
      Protobuf::write_varint($fp, $this->dw_);
    }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->bucket_)) {
      $l = strlen($this->bucket_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->key_)) {
      $l = strlen($this->key_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->rw_)) {
      $size += 1 + Protobuf::size_varint($this->rw_);
    }
    if (!is_null($this->vclock_)) {
      $l = strlen($this->vclock_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->r_)) {
      $size += 1 + Protobuf::size_varint($this->r_);
    }
    if (!is_null($this->w_)) {
      $size += 1 + Protobuf::size_varint($this->w_);
    }
    if (!is_null($this->pr_)) {
      $size += 1 + Protobuf::size_varint($this->pr_);
    }
    if (!is_null($this->pw_)) {
      $size += 1 + Protobuf::size_varint($this->pw_);
    }
    if (!is_null($this->dw_)) {
      $size += 1 + Protobuf::size_varint($this->dw_);
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->bucket_ === null) return false;
    if ($this->key_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('bucket_', $this->bucket_)
         . Protobuf::toString('key_', $this->key_)
         . Protobuf::toString('rw_', $this->rw_)
         . Protobuf::toString('vclock_', $this->vclock_)
         . Protobuf::toString('r_', $this->r_)
         . Protobuf::toString('w_', $this->w_)
         . Protobuf::toString('pr_', $this->pr_)
         . Protobuf::toString('pw_', $this->pw_)
         . Protobuf::toString('dw_', $this->dw_);
  }
  
  // required bytes bucket = 1;

  private $bucket_ = null;
  public function clearBucket() { $this->bucket_ = null; }
  public function hasBucket() { return $this->bucket_ !== null; }
  public function getBucket() { if($this->bucket_ === null) return ""; else return $this->bucket_; }
  public function setBucket($value) { $this->bucket_ = $value; }
  
  // required bytes key = 2;

  private $key_ = null;
  public function clearKey() { $this->key_ = null; }
  public function hasKey() { return $this->key_ !== null; }
  public function getKey() { if($this->key_ === null) return ""; else return $this->key_; }
  public function setKey($value) { $this->key_ = $value; }
  
  // optional uint32 rw = 3;

  private $rw_ = null;
  public function clearRw() { $this->rw_ = null; }
  public function hasRw() { return $this->rw_ !== null; }
  public function getRw() { if($this->rw_ === null) return 0; else return $this->rw_; }
  public function setRw($value) { $this->rw_ = $value; }
  
  // optional bytes vclock = 4;

  private $vclock_ = null;
  public function clearVclock() { $this->vclock_ = null; }
  public function hasVclock() { return $this->vclock_ !== null; }
  public function getVclock() { if($this->vclock_ === null) return ""; else return $this->vclock_; }
  public function setVclock($value) { $this->vclock_ = $value; }
  
  // optional uint32 r = 5;

  private $r_ = null;
  public function clearR() { $this->r_ = null; }
  public function hasR() { return $this->r_ !== null; }
  public function getR() { if($this->r_ === null) return 0; else return $this->r_; }
  public function setR($value) { $this->r_ = $value; }
  
  // optional uint32 w = 6;

  private $w_ = null;
  public function clearW() { $this->w_ = null; }
  public function hasW() { return $this->w_ !== null; }
  public function getW() { if($this->w_ === null) return 0; else return $this->w_; }
  public function setW($value) { $this->w_ = $value; }
  
  // optional uint32 pr = 7;

  private $pr_ = null;
  public function clearPr() { $this->pr_ = null; }
  public function hasPr() { return $this->pr_ !== null; }
  public function getPr() { if($this->pr_ === null) return 0; else return $this->pr_; }
  public function setPr($value) { $this->pr_ = $value; }
  
  // optional uint32 pw = 8;

  private $pw_ = null;
  public function clearPw() { $this->pw_ = null; }
  public function hasPw() { return $this->pw_ !== null; }
  public function getPw() { if($this->pw_ === null) return 0; else return $this->pw_; }
  public function setPw($value) { $this->pw_ = $value; }
  
  // optional uint32 dw = 9;

  private $dw_ = null;
  public function clearDw() { $this->dw_ = null; }
  public function hasDw() { return $this->dw_ !== null; }
  public function getDw() { if($this->dw_ === null) return 0; else return $this->dw_; }
  public function setDw($value) { $this->dw_ = $value; }
  
  // @@protoc_insertion_point(class_scope:DelReq)
}
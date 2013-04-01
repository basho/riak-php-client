<?php
namespace Riak\ProtoBuf;
use Riak\ProtoBuf;

// message PutReq
class PutReq {
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
      //var_dump("PutReq: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
        case 4:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->content_ = new Content($fp, $len);
          ASSERT('$len == 0');
          break;
        case 5:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->w_ = $tmp;
          
          break;
        case 6:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->dw_ = $tmp;
          
          break;
        case 7:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->returnBody_ = $tmp > 0 ? true : false;
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
          $this->ifNotModified_ = $tmp > 0 ? true : false;
          break;
        case 10:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->ifNoneMatch_ = $tmp > 0 ? true : false;
          break;
        case 11:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->returnHead_ = $tmp > 0 ? true : false;
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
    if (!is_null($this->vclock_)) {
      fwrite($fp, "\x1a");
      Protobuf::write_varint($fp, strlen($this->vclock_));
      fwrite($fp, $this->vclock_);
    }
    if (!is_null($this->content_)) {
      fwrite($fp, "\"");
      Protobuf::write_varint($fp, $this->content_->size()); // message
      $this->content_->write($fp);
    }
    if (!is_null($this->w_)) {
      fwrite($fp, "(");
      Protobuf::write_varint($fp, $this->w_);
    }
    if (!is_null($this->dw_)) {
      fwrite($fp, "0");
      Protobuf::write_varint($fp, $this->dw_);
    }
    if (!is_null($this->returnBody_)) {
      fwrite($fp, "8");
      Protobuf::write_varint($fp, $this->returnBody_ ? 1 : 0);
    }
    if (!is_null($this->pw_)) {
      fwrite($fp, "@");
      Protobuf::write_varint($fp, $this->pw_);
    }
    if (!is_null($this->ifNotModified_)) {
      fwrite($fp, "H");
      Protobuf::write_varint($fp, $this->ifNotModified_ ? 1 : 0);
    }
    if (!is_null($this->ifNoneMatch_)) {
      fwrite($fp, "P");
      Protobuf::write_varint($fp, $this->ifNoneMatch_ ? 1 : 0);
    }
    if (!is_null($this->returnHead_)) {
      fwrite($fp, "X");
      Protobuf::write_varint($fp, $this->returnHead_ ? 1 : 0);
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
    if (!is_null($this->vclock_)) {
      $l = strlen($this->vclock_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->content_)) {
      $l = $this->content_->size();
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->w_)) {
      $size += 1 + Protobuf::size_varint($this->w_);
    }
    if (!is_null($this->dw_)) {
      $size += 1 + Protobuf::size_varint($this->dw_);
    }
    if (!is_null($this->returnBody_)) {
      $size += 2;
    }
    if (!is_null($this->pw_)) {
      $size += 1 + Protobuf::size_varint($this->pw_);
    }
    if (!is_null($this->ifNotModified_)) {
      $size += 2;
    }
    if (!is_null($this->ifNoneMatch_)) {
      $size += 2;
    }
    if (!is_null($this->returnHead_)) {
      $size += 2;
    }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->bucket_ === null) return false;
    if ($this->content_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('bucket_', $this->bucket_)
         . Protobuf::toString('key_', $this->key_)
         . Protobuf::toString('vclock_', $this->vclock_)
         . Protobuf::toString('content_', $this->content_)
         . Protobuf::toString('w_', $this->w_)
         . Protobuf::toString('dw_', $this->dw_)
         . Protobuf::toString('returnBody_', $this->returnBody_)
         . Protobuf::toString('pw_', $this->pw_)
         . Protobuf::toString('ifNotModified_', $this->ifNotModified_)
         . Protobuf::toString('ifNoneMatch_', $this->ifNoneMatch_)
         . Protobuf::toString('returnHead_', $this->returnHead_);
  }
  
  // required bytes bucket = 1;

  private $bucket_ = null;
  public function clearBucket() { $this->bucket_ = null; }
  public function hasBucket() { return $this->bucket_ !== null; }
  public function getBucket() { if($this->bucket_ === null) return ""; else return $this->bucket_; }
  public function setBucket($value) { $this->bucket_ = $value; }
  
  // optional bytes key = 2;

  private $key_ = null;
  public function clearKey() { $this->key_ = null; }
  public function hasKey() { return $this->key_ !== null; }
  public function getKey() { if($this->key_ === null) return ""; else return $this->key_; }
  public function setKey($value) { $this->key_ = $value; }
  
  // optional bytes vclock = 3;

  private $vclock_ = null;
  public function clearVclock() { $this->vclock_ = null; }
  public function hasVclock() { return $this->vclock_ !== null; }
  public function getVclock() { if($this->vclock_ === null) return ""; else return $this->vclock_; }
  public function setVclock($value) { $this->vclock_ = $value; }
  
  // required .Content content = 4;

  private $content_ = null;
  public function clearContent() { $this->content_ = null; }
  public function hasContent() { return $this->content_ !== null; }
  public function getContent() { if($this->content_ === null) return null; else return $this->content_; }
  public function setContent(Content $value) { $this->content_ = $value; }
  
  // optional uint32 w = 5;

  private $w_ = null;
  public function clearW() { $this->w_ = null; }
  public function hasW() { return $this->w_ !== null; }
  public function getW() { if($this->w_ === null) return 0; else return $this->w_; }
  public function setW($value) { $this->w_ = $value; }
  
  // optional uint32 dw = 6;

  private $dw_ = null;
  public function clearDw() { $this->dw_ = null; }
  public function hasDw() { return $this->dw_ !== null; }
  public function getDw() { if($this->dw_ === null) return 0; else return $this->dw_; }
  public function setDw($value) { $this->dw_ = $value; }
  
  // optional bool return_body = 7;

  private $returnBody_ = null;
  public function clearReturnBody() { $this->returnBody_ = null; }
  public function hasReturnBody() { return $this->returnBody_ !== null; }
  public function getReturnBody() { if($this->returnBody_ === null) return false; else return $this->returnBody_; }
  public function setReturnBody($value) { $this->returnBody_ = $value; }
  
  // optional uint32 pw = 8;

  private $pw_ = null;
  public function clearPw() { $this->pw_ = null; }
  public function hasPw() { return $this->pw_ !== null; }
  public function getPw() { if($this->pw_ === null) return 0; else return $this->pw_; }
  public function setPw($value) { $this->pw_ = $value; }
  
  // optional bool if_not_modified = 9;

  private $ifNotModified_ = null;
  public function clearIfNotModified() { $this->ifNotModified_ = null; }
  public function hasIfNotModified() { return $this->ifNotModified_ !== null; }
  public function getIfNotModified() { if($this->ifNotModified_ === null) return false; else return $this->ifNotModified_; }
  public function setIfNotModified($value) { $this->ifNotModified_ = $value; }
  
  // optional bool if_none_match = 10;

  private $ifNoneMatch_ = null;
  public function clearIfNoneMatch() { $this->ifNoneMatch_ = null; }
  public function hasIfNoneMatch() { return $this->ifNoneMatch_ !== null; }
  public function getIfNoneMatch() { if($this->ifNoneMatch_ === null) return false; else return $this->ifNoneMatch_; }
  public function setIfNoneMatch($value) { $this->ifNoneMatch_ = $value; }
  
  // optional bool return_head = 11;

  private $returnHead_ = null;
  public function clearReturnHead() { $this->returnHead_ = null; }
  public function hasReturnHead() { return $this->returnHead_ !== null; }
  public function getReturnHead() { if($this->returnHead_ === null) return false; else return $this->returnHead_; }
  public function setReturnHead($value) { $this->returnHead_ = $value; }
  
  // @@protoc_insertion_point(class_scope:PutReq)
}

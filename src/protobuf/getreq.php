<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;


// message GetReq
class GetReq {
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
      //var_dump("GetReq: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->r_ = $tmp;
          
          break;
        case 4:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->pr_ = $tmp;
          
          break;
        case 5:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->basicQuorum_ = $tmp > 0 ? true : false;
          break;
        case 6:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->notfoundOk_ = $tmp > 0 ? true : false;
          break;
        case 7:
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
          $this->ifModified_ = $tmp;
          $limit-=$len;
          break;
        case 8:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->head_ = $tmp > 0 ? true : false;
          break;
        case 9:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->deletedvclock_ = $tmp > 0 ? true : false;
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
    if (!is_null($this->r_)) {
      fwrite($fp, "\x18");
      Protobuf::write_varint($fp, $this->r_);
    }
    if (!is_null($this->pr_)) {
      fwrite($fp, " ");
      Protobuf::write_varint($fp, $this->pr_);
    }
    if (!is_null($this->basicQuorum_)) {
      fwrite($fp, "(");
      Protobuf::write_varint($fp, $this->basicQuorum_ ? 1 : 0);
    }
    if (!is_null($this->notfoundOk_)) {
      fwrite($fp, "0");
      Protobuf::write_varint($fp, $this->notfoundOk_ ? 1 : 0);
    }
    if (!is_null($this->ifModified_)) {
      fwrite($fp, ":");
      Protobuf::write_varint($fp, strlen($this->ifModified_));
      fwrite($fp, $this->ifModified_);
    }
    if (!is_null($this->head_)) {
      fwrite($fp, "@");
      Protobuf::write_varint($fp, $this->head_ ? 1 : 0);
    }
    if (!is_null($this->deletedvclock_)) {
      fwrite($fp, "H");
      Protobuf::write_varint($fp, $this->deletedvclock_ ? 1 : 0);
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
    if (!is_null($this->r_)) {
      $size += 1 + Protobuf::size_varint($this->r_);
    }
    if (!is_null($this->pr_)) {
      $size += 1 + Protobuf::size_varint($this->pr_);
    }
    if (!is_null($this->basicQuorum_)) {
      $size += 2;
    }
    if (!is_null($this->notfoundOk_)) {
      $size += 2;
    }
    if (!is_null($this->ifModified_)) {
      $l = strlen($this->ifModified_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->head_)) {
      $size += 2;
    }
    if (!is_null($this->deletedvclock_)) {
      $size += 2;
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
         . Protobuf::toString('r_', $this->r_)
         . Protobuf::toString('pr_', $this->pr_)
         . Protobuf::toString('basicQuorum_', $this->basicQuorum_)
         . Protobuf::toString('notfoundOk_', $this->notfoundOk_)
         . Protobuf::toString('ifModified_', $this->ifModified_)
         . Protobuf::toString('head_', $this->head_)
         . Protobuf::toString('deletedvclock_', $this->deletedvclock_);
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
  
  // optional uint32 r = 3;

  private $r_ = null;
  public function clearR() { $this->r_ = null; }
  public function hasR() { return $this->r_ !== null; }
  public function getR() { if($this->r_ === null) return 0; else return $this->r_; }
  public function setR($value) { $this->r_ = $value; }
  
  // optional uint32 pr = 4;

  private $pr_ = null;
  public function clearPr() { $this->pr_ = null; }
  public function hasPr() { return $this->pr_ !== null; }
  public function getPr() { if($this->pr_ === null) return 0; else return $this->pr_; }
  public function setPr($value) { $this->pr_ = $value; }
  
  // optional bool basic_quorum = 5;

  private $basicQuorum_ = null;
  public function clearBasicQuorum() { $this->basicQuorum_ = null; }
  public function hasBasicQuorum() { return $this->basicQuorum_ !== null; }
  public function getBasicQuorum() { if($this->basicQuorum_ === null) return false; else return $this->basicQuorum_; }
  public function setBasicQuorum($value) { $this->basicQuorum_ = $value; }
  
  // optional bool notfound_ok = 6;

  private $notfoundOk_ = null;
  public function clearNotfoundOk() { $this->notfoundOk_ = null; }
  public function hasNotfoundOk() { return $this->notfoundOk_ !== null; }
  public function getNotfoundOk() { if($this->notfoundOk_ === null) return false; else return $this->notfoundOk_; }
  public function setNotfoundOk($value) { $this->notfoundOk_ = $value; }
  
  // optional bytes if_modified = 7;

  private $ifModified_ = null;
  public function clearIfModified() { $this->ifModified_ = null; }
  public function hasIfModified() { return $this->ifModified_ !== null; }
  public function getIfModified() { if($this->ifModified_ === null) return ""; else return $this->ifModified_; }
  public function setIfModified($value) { $this->ifModified_ = $value; }
  
  // optional bool head = 8;

  private $head_ = null;
  public function clearHead() { $this->head_ = null; }
  public function hasHead() { return $this->head_ !== null; }
  public function getHead() { if($this->head_ === null) return false; else return $this->head_; }
  public function setHead($value) { $this->head_ = $value; }
  
  // optional bool deletedvclock = 9;

  private $deletedvclock_ = null;
  public function clearDeletedvclock() { $this->deletedvclock_ = null; }
  public function hasDeletedvclock() { return $this->deletedvclock_ !== null; }
  public function getDeletedvclock() { if($this->deletedvclock_ === null) return false; else return $this->deletedvclock_; }
  public function setDeletedvclock($value) { $this->deletedvclock_ = $value; }
  
  // @@protoc_insertion_point(class_scope:GetReq)
}

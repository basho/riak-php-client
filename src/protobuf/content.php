<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

// message Content
class Content {
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
      //var_dump("Content: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
          $this->value_ = $tmp;
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
          $this->charset_ = $tmp;
          $limit-=$len;
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
          $this->contentEncoding_ = $tmp;
          $limit-=$len;
          break;
        case 5:
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
          $this->vtag_ = $tmp;
          $limit-=$len;
          break;
        case 6:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->links_[] = new Link($fp, $len);
          ASSERT('$len == 0');
          break;
        case 7:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->lastMod_ = $tmp;
          
          break;
        case 8:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->lastModUsecs_ = $tmp;
          
          break;
        case 9:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->usermeta_[] = new Pair($fp, $len);
          ASSERT('$len == 0');
          break;
        case 10:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->indexes_[] = new Pair($fp, $len);
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
    if (!is_null($this->value_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->value_));
      fwrite($fp, $this->value_);
    }
    if (!is_null($this->contentType_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->contentType_));
      fwrite($fp, $this->contentType_);
    }
    if (!is_null($this->charset_)) {
      fwrite($fp, "\x1a");
      Protobuf::write_varint($fp, strlen($this->charset_));
      fwrite($fp, $this->charset_);
    }
    if (!is_null($this->contentEncoding_)) {
      fwrite($fp, "\"");
      Protobuf::write_varint($fp, strlen($this->contentEncoding_));
      fwrite($fp, $this->contentEncoding_);
    }
    if (!is_null($this->vtag_)) {
      fwrite($fp, "*");
      Protobuf::write_varint($fp, strlen($this->vtag_));
      fwrite($fp, $this->vtag_);
    }
    if (!is_null($this->links_))
      foreach($this->links_ as $v) {
        fwrite($fp, "2");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
    if (!is_null($this->lastMod_)) {
      fwrite($fp, "8");
      Protobuf::write_varint($fp, $this->lastMod_);
    }
    if (!is_null($this->lastModUsecs_)) {
      fwrite($fp, "@");
      Protobuf::write_varint($fp, $this->lastModUsecs_);
    }
    if (!is_null($this->usermeta_))
      foreach($this->usermeta_ as $v) {
        fwrite($fp, "J");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
    if (!is_null($this->indexes_))
      foreach($this->indexes_ as $v) {
        fwrite($fp, "R");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
  }
  
  public function size() {
    $size = 0;
    if (!is_null($this->value_)) {
      $l = strlen($this->value_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->contentType_)) {
      $l = strlen($this->contentType_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->charset_)) {
      $l = strlen($this->charset_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->contentEncoding_)) {
      $l = strlen($this->contentEncoding_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->vtag_)) {
      $l = strlen($this->vtag_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->links_))
      foreach($this->links_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    if (!is_null($this->lastMod_)) {
      $size += 1 + Protobuf::size_varint($this->lastMod_);
    }
    if (!is_null($this->lastModUsecs_)) {
      $size += 1 + Protobuf::size_varint($this->lastModUsecs_);
    }
    if (!is_null($this->usermeta_))
      foreach($this->usermeta_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    if (!is_null($this->indexes_))
      foreach($this->indexes_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    return $size;
  }
  
  public function validateRequired() {
    if ($this->value_ === null) return false;
    return true;
  }
  
  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('value_', $this->value_)
         . Protobuf::toString('contentType_', $this->contentType_)
         . Protobuf::toString('charset_', $this->charset_)
         . Protobuf::toString('contentEncoding_', $this->contentEncoding_)
         . Protobuf::toString('vtag_', $this->vtag_)
         . Protobuf::toString('links_', $this->links_)
         . Protobuf::toString('lastMod_', $this->lastMod_)
         . Protobuf::toString('lastModUsecs_', $this->lastModUsecs_)
         . Protobuf::toString('usermeta_', $this->usermeta_)
         . Protobuf::toString('indexes_', $this->indexes_);
  }
  
  // required bytes value = 1;

  private $value_ = null;
  public function clearValue() { $this->value_ = null; }
  public function hasValue() { return $this->value_ !== null; }
  public function getValue() { if($this->value_ === null) return ""; else return $this->value_; }
  public function setValue($value) { $this->value_ = $value; }
  
  // optional bytes content_type = 2;

  private $contentType_ = null;
  public function clearContentType() { $this->contentType_ = null; }
  public function hasContentType() { return $this->contentType_ !== null; }
  public function getContentType() { if($this->contentType_ === null) return ""; else return $this->contentType_; }
  public function setContentType($value) { $this->contentType_ = $value; }
  
  // optional bytes charset = 3;

  private $charset_ = null;
  public function clearCharset() { $this->charset_ = null; }
  public function hasCharset() { return $this->charset_ !== null; }
  public function getCharset() { if($this->charset_ === null) return ""; else return $this->charset_; }
  public function setCharset($value) { $this->charset_ = $value; }
  
  // optional bytes content_encoding = 4;

  private $contentEncoding_ = null;
  public function clearContentEncoding() { $this->contentEncoding_ = null; }
  public function hasContentEncoding() { return $this->contentEncoding_ !== null; }
  public function getContentEncoding() { if($this->contentEncoding_ === null) return ""; else return $this->contentEncoding_; }
  public function setContentEncoding($value) { $this->contentEncoding_ = $value; }
  
  // optional bytes vtag = 5;

  private $vtag_ = null;
  public function clearVtag() { $this->vtag_ = null; }
  public function hasVtag() { return $this->vtag_ !== null; }
  public function getVtag() { if($this->vtag_ === null) return ""; else return $this->vtag_; }
  public function setVtag($value) { $this->vtag_ = $value; }
  
  // repeated .Link links = 6;

  private $links_ = null;
  public function clearLinks() { $this->links_ = null; }
  public function getLinksCount() { if ($this->links_ === null ) return 0; else return count($this->links_); }
  public function getLinks($index) { return $this->links_[$index]; }
  public function getLinksArray() { if ($this->links_ === null ) return array(); else return $this->links_; }
  public function setLinks($index, $value) {$this->links_[$index] = $value;	}
  public function addLinks($value) { $this->links_[] = $value; }
  public function addAllLinks(array $values) { foreach($values as $value) {$this->links_[] = $value;} }
  
  // optional uint32 last_mod = 7;

  private $lastMod_ = null;
  public function clearLastMod() { $this->lastMod_ = null; }
  public function hasLastMod() { return $this->lastMod_ !== null; }
  public function getLastMod() { if($this->lastMod_ === null) return 0; else return $this->lastMod_; }
  public function setLastMod($value) { $this->lastMod_ = $value; }
  
  // optional uint32 last_mod_usecs = 8;

  private $lastModUsecs_ = null;
  public function clearLastModUsecs() { $this->lastModUsecs_ = null; }
  public function hasLastModUsecs() { return $this->lastModUsecs_ !== null; }
  public function getLastModUsecs() { if($this->lastModUsecs_ === null) return 0; else return $this->lastModUsecs_; }
  public function setLastModUsecs($value) { $this->lastModUsecs_ = $value; }
  
  // repeated .Pair usermeta = 9;

  private $usermeta_ = null;
  public function clearUsermeta() { $this->usermeta_ = null; }
  public function getUsermetaCount() { if ($this->usermeta_ === null ) return 0; else return count($this->usermeta_); }
  public function getUsermeta($index) { return $this->usermeta_[$index]; }
  public function getUsermetaArray() { if ($this->usermeta_ === null ) return array(); else return $this->usermeta_; }
  public function setUsermeta($index, $value) {$this->usermeta_[$index] = $value;	}
  public function addUsermeta($value) { $this->usermeta_[] = $value; }
  public function addAllUsermeta(array $values) { foreach($values as $value) {$this->usermeta_[] = $value;} }
  
  // repeated .Pair indexes = 10;

  private $indexes_ = null;
  public function clearIndexes() { $this->indexes_ = null; }
  public function getIndexesCount() { if ($this->indexes_ === null ) return 0; else return count($this->indexes_); }
  public function getIndexes($index) { return $this->indexes_[$index]; }
  public function getIndexesArray() { if ($this->indexes_ === null ) return array(); else return $this->indexes_; }
  public function setIndexes($index, $value) {$this->indexes_[$index] = $value;	}
  public function addIndexes($value) { $this->indexes_[] = $value; }
  public function addAllIndexes(array $values) { foreach($values as $value) {$this->indexes_[] = $value;} }
  
  // @@protoc_insertion_point(class_scope:Content)
}

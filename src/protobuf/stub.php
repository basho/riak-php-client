<?php
namespace Riak\ProtoBuf;
use Riak\Protobuf;

class Stub {
  function __construct() {}
  function read() {} 
  function write() {}
  public function size() {return 0;}
  public function validateRequired() {return false;}
  public function __toString() { return ''; }
}

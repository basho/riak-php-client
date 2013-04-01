<?php
namespace Riak\PB;

class PutResp extends Message
{

 protected static $fields = array(
    "1"=>"Content",
    "2"=>"Bytes",
    "3"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"content",
    "2"=>"vclock",
    "3"=>"key",
  );
  
  protected $values = array(
    "1"=>array(),
    "2"=>null,
    "3"=>null,
  );
}
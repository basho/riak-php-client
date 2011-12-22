<?php
namespace Riak\PB;

class ErrorResp extends Message
{
  
  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Int",
  );
  
  protected static $fieldNames = array(
    "1"=>"errmsg",
    "2"=>"errcode",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
  );
}

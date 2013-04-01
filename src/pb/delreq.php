<?php
namespace Riak\PB;

class DelReq extends Message
{
  
  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
    "3"=>"Int",
    "4"=>"Bytes",
    "5"=>"Int",
    "6"=>"Int",
    "7"=>"Int",
    "8"=>"Int",
    "9"=>"Int",
  );
  
  protected static $fieldNames = array(
    "1"=>"bucket",
    "2"=>"key",
    "3"=>"rw",
    "4"=>"vclock",
    "5"=>"r",
    "6"=>"w",
    "7"=>"pr",
    "8"=>"pw",
    "9"=>"dw",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
    "3"=>null,
    "4"=>null,
    "5"=>null,
    "6"=>null,
    "7"=>null,
    "8"=>null,
    "9"=>null,
  );
}
<?php
namespace Riak\PB;

class GetReq extends Message
{
  
    protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
    "3"=>"Int",
    "4"=>"Int",
    "5"=>"Bool",
    "6"=>"Bool",
    "7"=>"Bytes",
    "8"=>"Bool",
    "9"=>"Bool",
  );
  
  protected static $fieldNames = array(
    "1"=>"bucket",
    "2"=>"key",
    "3"=>"r",
    "4"=>"pr",
    "5"=>"basic_quorum",
    "6"=>"notfound_ok",
    "7"=>"if_modified",
    "8"=>"head",
    "9"=>"deletedvclock",
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
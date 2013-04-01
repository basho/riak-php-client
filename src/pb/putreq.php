<?php
namespace Riak\PB;
/*
message PutReq {
    required bytes bucket = 1;
    optional bytes key = 2;
    optional bytes vclock = 3;
    required Content content = 4;
    optional uint32 w = 5;
    optional uint32 dw = 6;
    optional bool return_body = 7;
    optional uint32 pw = 8;
    optional bool if_not_modified = 9;
    optional bool if_none_match = 10;
    optional bool return_head = 11;
}
*/

class PutReq extends Message
{

  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
    "3"=>"Bytes",
    "4"=>"Content",
    "5"=>"Int",
    "6"=>"Int",
    "7"=>"Bool",
    "8"=>"Int",
    "9"=>"Bool",
    "10"=>"Bool",
    "11"=>"Bool",
  );
  
  protected static $fieldNames = array(
    "1"=>"bucket",
    "2"=>"key",
    "3"=>"vclock",
    "4"=>"content",
    "5"=>"w",
    "6"=>"dw",
    "7"=>"return_body",
    "8"=>"pw",
    "9"=>"if_not_modified",
    "10"=>"if_none_match",
    "11"=>"return_head",
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
    "10"=>null,
    "11"=>null,
  );
}
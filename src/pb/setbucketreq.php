<?php
namespace Riak\PB;

class SetBucketReq extends Message
{
 protected $wired_type = Message::WIRED_LENGTH_DELIMITED;
 
 protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"BucketProps",
  );
  
  protected static $fieldNames = array(
    "1"=>"bucket",
    "2"=>"props",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
  );
}

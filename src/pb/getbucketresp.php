<?php
namespace Riak\PB;

class GetBucketResp extends Message
{

  protected static $fields = array(
    "1"=>"BucketProps",
  );
  
  protected static $fieldNames = array(
    "1"=>"props",
  );
  
  protected $values = array(
    "1"=>null,
  );
}
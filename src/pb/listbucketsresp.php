<?php
namespace Riak\PB;

class ListBucketsResp extends Message
{

  protected static $fields = array(
    "1"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"buckets",
  );
  
  protected $values = array(
    "1"=>array(),
  );
}
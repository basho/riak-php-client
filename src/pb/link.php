<?php
namespace Riak\PB;

class Link extends Message
{
  
  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
    "3"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"bucket",
    "2"=>"key",
    "3"=>"tag",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
    "3"=>null,
  );
}

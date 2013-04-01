<?php
namespace Riak\PB;

class Pair extends Message
{
  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"key",
    "2"=>"value",
  );
  
  protected $values = array (
    "1"=>null,
    "2"=>null,
  );
}
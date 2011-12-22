<?php
namespace Riak\PB;

class MapRedResp extends Message
{
  
  protected static $fields = array(
    "1"=>"Int",
    "2"=>"Bytes",
    "3"=>"Bool",
  );
  
  protected static $fieldNames = array(
    "1"=>"phase",
    "2"=>"response",
    "3"=>"done",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
    "3"=>null,
  );
}
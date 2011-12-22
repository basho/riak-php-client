<?php
namespace Riak\PB;

class MapRedReq extends Message
{

  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"request",
    "2"=>"content_type",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
  );
}
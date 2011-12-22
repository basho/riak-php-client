<?php
namespace Riak\PB;

class ListKeysReq extends Message
{

  protected static $fields = array(
    "1"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"bucket",
  );
  
  protected $values = array(
    "1"=>null,
  );
}
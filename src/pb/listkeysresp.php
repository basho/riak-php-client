<?php
namespace Riak\PB;

class ListKeysResp extends Message
{

  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bool",
  );
  
  protected static $fieldNames = array(
    "1"=>"keys",
    "2"=>"done",
  );
  
  protected $values = array(
    "1"=>array(),
    "2"=>null,
  );
}
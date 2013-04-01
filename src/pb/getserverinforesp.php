<?php
namespace Riak\PB;

class GetServerInfoResp extends Message
{
  
  protected static $fields = array(
    "1"=>"Bytes",
    "2"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"node",
    "2"=>"server_version",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
  );
}
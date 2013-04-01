<?php
namespace Riak\PB;

class GetClientIdResp extends Message
{
  
  protected static $fields = array(
    "1"=>"Bytes",
  );
  
  protected static $fieldNames = array(
    "1"=>"client_id",
  );
  
  protected $values = array(
    "1"=>null,
  );
}
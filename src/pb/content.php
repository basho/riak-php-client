<?php
namespace Riak\PB;

class Content extends Message
{
  protected static $fields = array (
    "1"=>"Bytes",
    "2"=>"Bytes",
    "3"=>"Bytes",
    "4"=>"Bytes",
    "5"=>"Bytes",
    "6"=>"Link",
    "7"=>"Int",
    "8"=>"Int",
    "9"=>"Pair",
    "10"=>"Pair",
  );
  
  protected static $fieldNames = array(
    "1"=>"value",
    "2"=>"content_type",
    "3"=>"charset",
    "4"=>"content_encoding",
    "5"=>"vtag",
    "6"=>"links",
    "7"=>"last_mod",
    "8"=>"last_mod_usecs",
    "9"=>"usermeta",
    "10"=>"indexes",
  );
  
  protected $values = array(
    "1"=>null,
    "2"=>null,
    "3"=>null,
    "4"=>null,
    "5"=>null,
    "6"=>array(),
    "7"=>null,
    "8"=>null,
    "9"=>array(),
    "10"=>array(),
  );
}